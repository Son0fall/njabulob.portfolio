from flask import Flask, request, jsonify
from flask_cors import CORS
from google import genai
import requests
import os

app = Flask(__name__)
CORS(app)

# Configuration - Use environment variables or hardcode them
GEMINI_API_KEY = os.getenv('GEMINI_API_KEY', 'AIzaSyDyh1m33tGRMJKdHtJd90k9GovO9Mq4WRA')
GOOGLE_API_KEY = os.getenv('GOOGLE_API_KEY', 'AIzaSyBulRicQhaBHhkgRHjLIyJJj3xn4tEJ-aU') # Set this in PythonAnywhere environment
GOOGLE_CSE_ID = os.getenv('GOOGLE_CSE_ID', 'AIzaSyBulRicQhaBHhkgRHjLIyJJj3xn4tEJ-aU')   # Set this in PythonAnywhere environment

client = genai.Client(api_key=GEMINI_API_KEY)

def google_search(query):
    if not GOOGLE_API_KEY or not GOOGLE_CSE_ID:
        return []
    url = f"https://www.googleapis.com/customsearch/v1?key={GOOGLE_API_KEY}&cx={GOOGLE_CSE_ID}&q={query}"
    try:
        response = requests.get(url, timeout=5)
        if response.status_code == 200:
            data = response.json()
            items = data.get('items', [])
            return [{"title": it.get('title'), "snippet": it.get('snippet'), "link": it.get('link')} for it in items[:3]]
    except Exception as e:
        print(f"Search error: {e}")
    return []

@app.route('/chat', methods=['POST'])
def chat():
    data = request.json
    if not data:
        return jsonify({"response": "Invalid request. No JSON data found."}), 400
        
    user_message = data.get('message', '')
    profile = data.get('profile', {})
    
    if not user_message:
        return jsonify({"response": "I didn't receive any message."})

    # 1. Search for info about Njabulo B Mavuso if relevant
    # We always include search to fulfill the user's request for "going to all social pages and google"
    search_query = f"Njabulo B Mavuso {user_message}"
    search_results = google_search(search_query)
    
    # 2. Prepare prompt for Gemini
    prompt = f"""
    You are an AI assistant for Njabulo B Mavuso's portfolio. 
    Your goal is to answer questions about him professionally and accurately.
    
    Portfolio Profile (Primary Source):
    {profile}
    
    External Search Results (Secondary Source):
    {search_results}
    
    User Question: {user_message}
    
    Instructions:
    - Use the profile information as the primary source.
    - Use search results to supplement information, especially for social media or recent activities.
    - If you find social media links (LinkedIn, GitHub, etc.) in the profile or search results, use them to guide the user.
    - If you don't know something, be honest but polite.
    - Keep the tone friendly and professional.
    - Provide a concise but helpful response.
    """
    
    try:
        response = client.models.generate_content(
            model='gemini-1.5-flash',
            contents=prompt
        )
        return jsonify({
            "response": response.text,
            "sources": search_results
        })
    except Exception as e:
        return jsonify({"error": str(e), "response": "I'm having trouble thinking right now. Please try again later."})

@app.route('/', methods=['GET'])
def home():
    return "Njabulo B Mavuso Chatbot API is running!"

if __name__ == '__main__':
    # Local testing
    app.run(port=5000, debug=True)
