<?php
// Start session for visitor tracking
function get_session_save_path_value(): string {
    $raw = (string) ini_get('session.save_path');
    if ($raw === '') return '';
    if (strpos($raw, ';') === false) return $raw;
    $parts = array_values(array_filter(array_map('trim', explode(';', $raw)), static fn($p) => $p !== ''));
    return $parts ? $parts[count($parts) - 1] : '';
}

$sessionSavePath = get_session_save_path_value();
if ($sessionSavePath === '' || !is_dir($sessionSavePath) || !is_writable($sessionSavePath)) {
    $fallback = sys_get_temp_dir();
    if ($fallback && is_dir($fallback) && is_writable($fallback)) {
        ini_set('session.save_path', $fallback);
    } else {
        $localTmp = __DIR__ . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($localTmp)) {
            @mkdir($localTmp, 0777, true);
        }
        if (is_dir($localTmp) && is_writable($localTmp)) {
            ini_set('session.save_path', $localTmp);
        }
    }
}

session_start();
require_once 'config.php'; // Ensure config.php exists or remove if not needed

// Simple visitor tracking
$visitorFile = 'visitor-data.json';
$today = date('Y-m-d');

if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    
    if (file_exists($visitorFile)) {
        $data = json_decode(file_get_contents($visitorFile), true);
    } else {
        $data = ['total' => 0, 'daily' => []];
    }
    
    $data['total']++;
    $data['daily'][$today] = ($data['daily'][$today] ?? 0) + 1;
    $data['last_updated'] = date('Y-m-d H:i:s');
    
    file_put_contents($visitorFile, json_encode($data, JSON_PRETTY_PRINT));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Njabulo B. Mavuso" />
<meta property="og:url" content="https://sonofall.great-site.net" />
    <meta name="google-site-verification" content="6ura3L2biwC_TouyM-RZA0qWeMNMuoM_xpjlHwkd3oY" />
<meta property="og:image" content="link-to-your-profile-photo.jpg" />
    <meta name="description" content="Official portfolio of Njabulo B Mavuso, featuring IT Personel, IT Engineer.">
    <title>Njabulo Mavuso | CSPro Programmer & IT Specialist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* style.css - Complete Portfolio Styling */

/* Reset & Base Styles */
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary: #0ea5e9;
    --accent: #10b981;
    --dark: #1e293b;
    --dark-light: #334155;
    --light: #f8fafc;
    --gray: #64748b;
    --gray-light: #e2e8f0;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    
    --font-main: 'Inter', sans-serif;
    --font-heading: 'Poppins', sans-serif;
    
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    
    --radius: 0.5rem;
    --radius-lg: 1rem;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
    scroll-padding-top: 80px;
}

body {
    font-family: var(--font-main);
    background-color: var(--light);
    color: var(--dark);
    line-height: 1.6;
    overflow-x: hidden;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Typography */
h1, h2, h3, h4 {
    font-family: var(--font-heading);
    font-weight: 600;
    line-height: 1.2;
}

h1 { font-size: 3.5rem; }
h2 { font-size: 2.5rem; }
h3 { font-size: 1.5rem; }

.section {
    padding: 5rem 0;
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.section-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background: var(--primary);
    margin: 1rem auto;
    border-radius: 2px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.8rem;
    border-radius: var(--radius);
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-family: var(--font-main);
}

.btn-primary {
    background: var(--primary);
    color: white;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
}

.btn-secondary {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-secondary:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1001;
    background: var(--primary);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    display: none;
}

.mobile-menu-toggle:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
}

/* Sidebar (Mobile Only) */
.sidebar {
    position: fixed;
    top: 0;
    left: -300px;
    width: 300px;
    height: 100vh;
    background: white;
    z-index: 1000;
    transition: left 0.3s ease;
    box-shadow: var(--shadow-xl);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.sidebar.active {
    left: 0;
}

.sidebar-header {
    padding: 2rem;
    background: var(--primary);
    color: white;
    position: relative;
}

.close-sidebar {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s;
}

.close-sidebar:hover {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-profile {
    text-align: center;
}

.profile-image {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    color: var(--dark);
    text-decoration: none;
    border-radius: var(--radius);
    transition: all 0.3s;
}

.sidebar-nav a:hover,
.sidebar-nav a.active {
    background: var(--gray-light);
    color: var(--primary);
}

.sidebar-nav a.download-cv {
    background: var(--primary);
    color: white;
    margin-top: 1rem;
}

.sidebar-nav a.download-cv:hover {
    background: var(--primary-dark);
}

/* Top Header (Desktop) */
.top-header {
    background: white;
    box-shadow: var(--shadow);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
}

.logo h1 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.tagline {
    color: var(--gray);
    font-size: 0.9rem;
}

.desktop-nav {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.desktop-nav a {
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 0;
    position: relative;
    transition: color 0.3s;
}

.desktop-nav a:hover,
.desktop-nav a.active {
    color: var(--primary);
}

.desktop-nav a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: width 0.3s;
}

.desktop-nav a:hover::after,
.desktop-nav a.active::after {
    width: 100%;
}

.btn-nav {
    background: var(--primary);
    color: white;
    padding: 0.6rem 1.5rem;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-nav:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

/* Hero Section */
.hero {
    padding: 10rem 0 5rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    position: relative;
    overflow: hidden;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-title {
    margin-bottom: 1rem;
}

.greeting {
    display: block;
    font-size: 1.2rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.name {
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.hero-subtitle {
    font-size: 1.5rem;
    font-weight: 400;
    margin-bottom: 1.5rem;
    color: var(--dark-light);
}

.highlight {
    color: var(--primary);
    font-weight: 600;
}

.hero-description {
    font-size: 1.1rem;
    color: var(--gray);
    margin-bottom: 2rem;
    max-width: 500px;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.floating-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    position: relative;
}

.card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    text-align: center;
    transition: all 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.card i {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.card h3 {
    font-size: 1rem;
    margin: 0;
}

.card-1 { transform: rotate(-3deg); }
.card-2 { transform: rotate(2deg); margin-top: 1rem; }
.card-3 { grid-column: span 2; transform: rotate(-1deg); }

/* About Section */
.about-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 4rem;
    align-items: start;
}

.intro {
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 2rem;
    color: var(--dark-light);
}

.about-points {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.point {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.point i {
    color: var(--primary);
    font-size: 1.5rem;
    margin-top: 0.25rem;
}

.point h3 {
    margin-bottom: 0.25rem;
}

.point p {
    color: var(--gray);
    font-size: 0.95rem;
}

.journey {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: var(--radius);
    border-left: 4px solid var(--primary);
}

.journey h3 {
    margin-bottom: 1rem;
    color: var(--primary);
}

.journey p {
    color: var(--dark-light);
    line-height: 1.7;
}

.about-image {
    position: relative;
}

.image-container {
    position: sticky;
    top: 100px;
}

.image-placeholder {
    width: 250px;
    height: 250px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    font-weight: bold;
    color: white;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.image-decoration {
    position: absolute;
    top: 20px;
    left: 20px;
    right: -20px;
    bottom: -20px;
    border: 2px solid var(--primary);
    border-radius: var(--radius);
    opacity: 0.3;
}

/* Services Section */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.service-card {
    background: white;
    padding: 2.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border-bottom: 4px solid transparent;
}

.service-card:hover {
    transform: translateY(-10px);
    border-bottom-color: var(--primary);
    box-shadow: var(--shadow-xl);
}

.service-icon {
    width: 70px;
    height: 70px;
    background: #e0f2fe;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.service-card:hover .service-icon {
    background: var(--primary);
    color: white;
    transform: rotateY(360deg);
}

.service-card h3 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.service-card p {
    color: var(--gray);
    margin-bottom: 1.5rem;
    line-height: 1.7;
}

.service-list {
    list-style: none;
    padding: 0;
}

.service-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    color: var(--dark-light);
    font-size: 0.95rem;
}

.service-list li i {
    color: var(--success);
    font-size: 0.8rem;
}

.timeline {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, var(--primary), var(--secondary));
}

.timeline-item {
    position: relative;
    margin-bottom: 3rem;
    padding-left: 80px;
}

.timeline-date {
    position: absolute;
    left: 0;
    top: 0;
    background: var(--primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--radius);
    font-weight: 600;
    min-width: 80px;
    text-align: center;
}

.timeline-content {
    background: white;
    padding: 1.9rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    position: relative;
}

.timeline-content::before {
    content: '';
    position: absolute;
    left: -20px;
    top: 20px;
    width: 20px;
    height: 3px;
    background: var(--primary);
}
.timeline-content{
    left: 50px;
}

.timeline-header {
    margin-bottom: 1rem;
}

.timeline-header h3 {
    color: var(--primary);
    margin-bottom: 0.25rem;
}

.company {
    color: var(--gray);
    font-weight: 500;
    font-size: 0.95rem;
}

.timeline-description {
    color: var(--dark-light);
    margin-bottom: 1rem;
    line-height: 1.7;
}

.timeline-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag {
    background: #e0f2fe;
    color: var(--primary);
    padding: 0.3rem 0.8rem;
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    font-weight: 500;
}

/* Why Hire Me Section */
.why-hire-section {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    overflow: hidden;
}

.why-hire-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 4rem;
    align-items: center;
}

.value-propositions {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-top: 2.5rem;
}

.value-item {
    display: flex;
    gap: 1.5rem;
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
}

.value-item:hover {
    transform: translateX(10px);
    box-shadow: var(--shadow-lg);
}

.value-icon {
    width: 50px;
    height: 50px;
    background: #e0f2fe;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary);
    flex-shrink: 0;
}

.value-info h3 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
    color: var(--dark);
}

.value-info p {
    color: var(--gray);
    font-size: 0.95rem;
    line-height: 1.6;
}

.why-hire-stats {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.hire-stat-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary);
    transition: all 0.3s ease;
}

.hire-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.hire-stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: 0.25rem;
}

.hire-stat-label {
    color: var(--gray);
    font-weight: 500;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

@media (max-width: 1024px) {
    .why-hire-content {
        grid-template-columns: 1fr;
    }
    
    .why-hire-stats {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .hire-stat-card {
        flex: 1;
        min-width: 200px;
    }
}

.skill-category {
    background: white;
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.category-title {
    color: var(--primary);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e2e8f0;
}

.skill-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.skill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius);
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: all 0.3s ease;
}

.skill:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: rgba(37, 99, 235, 0.35);
}

.skill-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.skill-name {
    font-weight: 500;
}

.skill-bar {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.skill-level {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 4px;
    width: 0;
    transition: width 1.5s ease;
}

.skill-percent {
    color: var(--gray);
    font-weight: 600;
}

/* Projects Section */
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.project-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: all 0.3s;
    border-top: 4px solid var(--primary);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.project-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.project-card.in-progress {
    border-top-color: var(--warning);
    position: relative;
    overflow: hidden;
}

.status-badge {
    position: absolute;
    top: 15px;
    right: -30px;
    background: var(--warning);
    color: white;
    padding: 5px 40px;
    transform: rotate(45deg);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.project-icon {
    width: 60px;
    height: 60px;
    background: #e0f2fe;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: var(--primary);
    font-size: 1.5rem;
}

.project-title {
    color: var(--dark);
    margin-bottom: 1rem;
}

.project-description {
    color: var(--gray);
    margin-bottom: 1.5rem;
    flex: 1;
    line-height: 1.7;
}

.project-links {
    margin-bottom: 1.5rem;
}

.project-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.project-link:hover {
    color: var(--primary-dark);
    gap: 0.7rem;
}

.project-tech {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.project-tech span {
    background: #f1f5f9;
    color: var(--dark-light);
    padding: 0.3rem 0.8rem;
    border-radius: var(--radius-full);
    font-size: 0.85rem;
}

/* Contact Section */
.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    max-width: 1000px;
    margin: 0 auto;
}

.contact-subtitle {
    color: var(--primary);
    margin-bottom: 1rem;
}

.contact-description {
    color: var(--gray);
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.contact-method {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: var(--radius);
    text-decoration: none;
    color: var(--dark);
    transition: all 0.3s;
    border: 1px solid transparent;
}

.contact-method:hover {
    border-color: var(--primary);
    transform: translateX(5px);
}

.contact-method i {
    color: var(--primary);
    font-size: 1.2rem;
    width: 40px;
}

.contact-method h4 {
    margin-bottom: 0.25rem;
    color: var(--dark);
}

.contact-method p {
    color: var(--gray);
    font-size: 0.95rem;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius);
    font-family: var(--font-main);
    transition: all 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
    resize: vertical;
}

.form-message {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: var(--radius);
    display: none;
}

.form-message.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    display: block;
}

.form-message.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
    display: block;
}

/* CV Section */
.cv-section {
    background: #f8fafc;
}

.section-subtitle {
    text-align: center;
    color: var(--gray);
    margin-bottom: 3rem;
}

.cv-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto 3rem;
}

.cv-option {
    background: white;
    padding: 2rem;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    text-decoration: none;
    color: var(--dark);
    transition: all 0.3s;
    box-shadow: var(--shadow);
    border-top: 4px solid transparent;
}

.cv-option:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.cv-option:nth-child(1) {
    border-color: #ef4444;
}

.cv-option:nth-child(2) {
    border-color: var(--primary);
}

.cv-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.cv-icon.pdf {
    background: #ef4444;
}

.cv-icon.word {
    background: var(--primary);
}

.cv-info {
    flex: 1;
}

.cv-info h3 {
    margin-bottom: 0.5rem;
}

.cv-info p {
    color: var(--gray);
    font-size: 0.95rem;
}

.cv-action {
    color: var(--primary);
    font-size: 1.2rem;
}

.cv-tips {
    background: white;
    padding: 2rem;
    border-radius: var(--radius);
    max-width: 800px;
    margin: 0 auto;
    border-left: 4px solid var(--accent);
}

.cv-tips h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--accent);
    margin-bottom: 1rem;
}

.cv-tips ul {
    list-style: none;
    padding-left: 0;
}

.cv-tips li {
    padding: 0.5rem 0;
    color: var(--dark-light);
    position: relative;
    padding-left: 1.5rem;
}

.cv-tips li::before {
    content: '•';
    color: var(--accent);
    position: absolute;
    left: 0;
    font-weight: bold;
}

/* Footer */
.footer {
    background: var(--dark);
    color: white;
    padding: 4rem 0 2rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    margin-bottom: 3rem;
}

.footer-about h3 {
    color: white;
    margin-bottom: 1rem;
}

.footer-about p {
    color: #cbd5e1;
    line-height: 1.7;
}

.footer-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 2rem;
}

.link-group h4 {
    color: white;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.link-group a {
    display: block;
    color: #cbd5e1;
    text-decoration: none;
    margin-bottom: 0.5rem;
    transition: color 0.3s;
}

.link-group a:hover {
    color: white;
}

.visitor-stats {
    background: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.visitor-stats h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    margin-bottom: 1rem;
}

.visitor-stats p {
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.visitor-stats strong {
    color: white;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: #94a3b8;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .footer-bottom {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
}

.footer-bottom p {
    margin: 0;
}

.footer-copyright {
    flex: 1;
    text-align: center;
}

.footer-datetime {
    text-align: right;
    min-width: 150px;
}

.footer-social-icons {
    display: flex;
    gap: 1rem;
    min-width: 150px;
}

.footer-social-icons a {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    transition: all 0.3s ease;
}

.footer-social-icons a:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.16);
    border-color: rgba(255, 255, 255, 0.22);
}

.footer-social-icons a.linkedin { color: #0a66c2; }
.footer-social-icons a.github { color: #f8fafc; }
.footer-social-icons a.whatsapp { color: #25d366; }
.footer-social-icons a.email { color: #ea4335; }

.footer-social-icons a.linkedin:hover { box-shadow: 0 10px 25px rgba(10, 102, 194, 0.25); }
.footer-social-icons a.github:hover { box-shadow: 0 10px 25px rgba(248, 250, 252, 0.18); }
.footer-social-icons a.whatsapp:hover { box-shadow: 0 10px 25px rgba(37, 211, 102, 0.22); }
.footer-social-icons a.email:hover { box-shadow: 0 10px 25px rgba(234, 67, 53, 0.22); }

/* Chatbot Widget */
.chatbot-widget {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
}

.chatbot-container {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 350px;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    display: none;
    flex-direction: column;
    max-height: 500px;
}

.chatbot-container.active {
    display: flex;
}

.chatbot-header {
    background: var(--primary);
    color: white;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header h3 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-size: 1.1rem;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s;
}

.chatbot-close:hover {
    background: rgba(255, 255, 255, 0.1);
}

.chatbot-body {
    display: flex;
    flex-direction: column;
    height: 400px;
}

.chatbot-messages {
    flex: 1;
    padding: 1.5rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message {
    max-width: 80%;
    padding: 0.8rem 1rem;
    border-radius: var(--radius);
    line-height: 1.5;
}

.message.bot {
    background: #f1f5f9;
    color: var(--dark);
    align-self: flex-start;
}

.message.user {
    background: var(--primary);
    color: white;
    align-self: flex-end;
}

.chatbot-input {
    padding: 1rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 0.5rem;
}

.chatbot-input input {
    flex: 1;
    padding: 0.8rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius);
    font-family: var(--font-main);
    transition: all 0.3s;
}

.chatbot-input input:focus {
    outline: none;
    border-color: var(--primary);
}

.chatbot-input button {
    background: var(--primary);
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chatbot-input button:hover {
    background: var(--primary-dark);
}

.chatbot-toggle {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 60px;
    height: 60px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s;
    z-index: 1001;
}

.chatbot-toggle:hover {
    background: var(--primary-dark);
    transform: scale(1.1);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-content,
    .about-content,
    .contact-content {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .hero {
        padding: 8rem 0 4rem;
    }
    
    .floating-cards {
        max-width: 500px;
        margin: 0 auto;
    }
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex;
    }
    
    .desktop-nav {
        display: none;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .section {
        padding: 3rem 0;
    }
    
    .timeline::before {
        left: 15px;
    }
    
    .timeline-item {
        padding-left: 60px;
    }
    
    .timeline-date {
        min-width: 60px;
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
    
    .chatbot-container {
        width: calc(100vw - 2rem);
        right: -1rem;
    }
}

@media (max-width: 480px) {
    .hero-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .floating-cards {
        grid-template-columns: 1fr;
    }
    
    .card-3 {
        grid-column: 1;
    }
    
    .skills-grid,
    .projects-grid,
    .cv-options {
        grid-template-columns: 1fr;
    }
}

/* ====================
   ADDITIONAL STYLES
   ==================== */

/* Logo Styling */
.logo-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
    object-fit: cover;
    border: 3px solid var(--primary);
}

.logo {
    display: flex;
    align-items: center;
}

.logo-text {
    display: flex;
    flex-direction: column;
}

/* Profile Images */
.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Hero Typing Effect */
.typed-text {
    color: var(--primary);
    font-weight: 600;
}

.typed-cursor {
    color: var(--primary);
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

/* Hero Banner Navigation */
.hero-banner {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    padding: 1.5rem 0;
    margin-top: 3rem;
    border-radius: 20px 20px 0 0;
}

.banner-nav {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 1rem;
}

.banner-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: white;
    text-decoration: none;
    padding: 1rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    min-width: 100px;
}

.banner-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-5px);
}

.banner-item i {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
}

.banner-item span {
    font-size: 0.9rem;
    font-weight: 500;
}

/* About Section Profile Photo */
.profile-photo {
    width: 100%;
    max-width: 350px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1;
    border: 8px solid white;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: block;
    margin: 0 auto;
}

.profile-photo:hover {
    transform: scale(1.02) rotate(2deg);
    border-color: var(--primary);
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2);
}

.image-container {
    position: relative;
    padding: 20px;
    background: white;
    border-radius: 30px;
    box-shadow: var(--shadow-lg);
    border: 2px solid rgba(37, 99, 235, 0.1);
    overflow: visible;
}

.image-container::before {
    content: '';
    position: absolute;
    top: -15px;
    left: -15px;
    right: -15px;
    bottom: -15px;
    border: 2px dashed var(--primary);
    border-radius: 40px;
    z-index: -1;
    opacity: 0.3;
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.experience-badge {
    position: absolute;
    bottom: -20px;
    right: -10px;
    background: var(--primary);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 20px;
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    line-height: 1;
}

.exp-years {
    font-size: 1.8rem;
    font-weight: 800;
}

.exp-text {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Experience Bounce Animation */
.bounce-card {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.bounce-card.visible {
    opacity: 1;
    transform: translateY(0);
}

.bounce-card:hover {
    transform: translateY(-10px);
}

/* References Section */
.references-section {
    background: #f8fafc;
}

.references-container {
    max-width: 1000px;
    margin: 0 auto;
}

.reference-selector {
    margin-bottom: 3rem;
}

.reference-thumbnails {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.thumbnail-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 1rem;
    border-radius: 10px;
}

.thumbnail-item:hover {
    background: rgba(37, 99, 235, 0.05);
}

.thumbnail-item.active {
    background: rgba(37, 99, 235, 0.1);
}

.thumbnail-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 0.5rem;
    position: relative;
}

.thumbnail-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Bouncing Border Effect */
.bounce-border {
    animation: borderPulse 2s infinite;
    border: 3px solid var(--primary);
}

@keyframes borderPulse {
    0%, 100% {
        border-color: var(--primary);
        transform: scale(1);
    }
    50% {
        border-color: var(--secondary);
        transform: scale(1.05);
    }
}

.thumbnail-name {
    font-weight: 500;
    color: var(--dark);
    text-align: center;
}

/* Reference Cards */
.reference-cards {
    position: relative;
    min-height: 400px;
}

.reference-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    display: none;
    animation: fadeIn 0.5s ease;
}

.reference-card.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.reference-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.reference-header .reference-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--primary);
}

.reference-header .reference-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.reference-title h3 {
    color: var(--primary);
    margin-bottom: 0.25rem;
}

.reference-title h4 {
    color: var(--gray);
    font-weight: 500;
    font-size: 1rem;
}

.reference-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.8rem;
    background: #f8fafc;
    border-radius: 8px;
}

.detail-item i {
    color: var(--primary);
    width: 20px;
}

.detail-item div {
    flex: 1;
}

.reference-quote {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid var(--primary);
    position: relative;
}

.reference-quote i {
    color: var(--primary);
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    display: block;
}

.reference-quote p {
    font-style: italic;
    color: var(--dark-light);
    line-height: 1.7;
}

/* CV Section Updates */
.file-size {
    display: block;
    color: var(--gray);
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

/* Footer Updates */
.footer-logo img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 1rem;
    border: 3px solid white;
}

.footer-about {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

/* Chatbot Suggestions */
.chatbot-suggestions {
    padding: 1rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.chatbot-suggestions span {
    color: var(--gray);
    font-size: 0.85rem;
    margin-right: 0.5rem;
}

.suggestion-btn {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: var(--dark);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
}

.suggestion-btn:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.notification-dot {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 10px;
    height: 10px;
    background: var(--danger);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

/* Responsive Design Updates */
@media (max-width: 768px) {
    .hero-banner {
        padding: 1rem;
        margin-top: 2rem;
    }
    
    .banner-nav {
        gap: 0.5rem;
    }
    
    .banner-item {
        min-width: 80px;
        padding: 0.8rem;
    }
    
    .banner-item i {
        font-size: 1.5rem;
    }
    
    .reference-thumbnails {
        gap: 1rem;
    }
    
    .thumbnail-image {
        width: 80px;
        height: 80px;
    }
    
    .reference-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .reference-details {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .logo-img {
        width: 40px;
        height: 40px;
    }
    
    .logo-text h1 {
        font-size: 1.2rem;
    }
    
    .logo-text .tagline {
        font-size: 0.8rem;
    }
    
    .banner-item {
        min-width: 60px;
        padding: 0.5rem;
    }
    
    .banner-item i {
        font-size: 1.2rem;
    }
    
    .banner-item span {
        font-size: 0.8rem;
    }
}

/* ====================
   HERO SECTION WITH BACKGROUND IMAGE
   ==================== */

/* Hero Section with Background Image */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    color: white;
    overflow: hidden;
    padding-top: 80px;
    background: 
        /* Dark overlay for better text readability */
        linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
        /* Your professional background image */
        url('assets/images/hero-background.jpg') no-repeat center center/cover;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -2;
    background: 
        /* Gradient overlay for depth */
        linear-gradient(135deg, 
            rgba(37, 99, 235, 0.3) 0%, 
            rgba(14, 165, 233, 0.2) 50%,
            rgba(16, 185, 129, 0.2) 100%);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        /* Dark gradient overlay for text contrast */
        linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.8) 0%,
            rgba(0, 0, 0, 0.5) 50%,
            rgba(0, 0, 0, 0.8) 100%
        );
    z-index: -1;
}

/* Hero Content Layout */
.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    width: 100%;
    position: relative;
    z-index: 2;
}

/* Hero Text Styling */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    overflow: hidden;
    animation: slideInLeft 0.8s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.badge-text {
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.badge-pulse {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.3);
    }
}

.hero-title {
    margin-bottom: 1rem;
    animation: slideInLeft 0.8s ease-out 0.2s both;
}

.greeting {
    display: block;
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0.5rem;
    font-weight: 400;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

.name {
    display: block;
    font-size: 3.5rem;
    background: linear-gradient(90deg, #ffffff, #e0f2fe, #a5f3fc);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    line-height: 1.1;
    margin-bottom: 0.5rem;
    text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    animation: glow 3s ease-in-out infinite alternate;
}

@keyframes glow {
    from {
        text-shadow: 
            0 0 5px #fff,
            0 0 10px #3b82f6,
            0 0 15px #3b82f6,
            0 0 20px #3b82f6;
    }
    to {
        text-shadow: 
            0 0 10px #fff,
            0 0 20px #10b981,
            0 0 30px #10b981,
            0 0 40px #10b981;
    }
}

.hero-subtitle {
    font-size: 1.8rem;
    font-weight: 400;
    margin-bottom: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    animation: slideInLeft 0.8s ease-out 0.4s both;
}

.typed-text {
    color: #10b981;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.typed-cursor {
    color: #10b981;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

.hero-description {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 2rem;
    line-height: 1.7;
    max-width: 500px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    animation: slideInLeft 0.8s ease-out 0.6s both;
}

/* Hero Statistics */
.hero-stats {
    display: flex;
    gap: 2rem;
    margin: 2rem 0;
    flex-wrap: wrap;
    animation: slideInLeft 0.8s ease-out 0.8s both;
}

.stat-item {
    text-align: center;
    min-width: 100px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    line-height: 1;
    margin-bottom: 0.5rem;
    background: linear-gradient(90deg, #3b82f6, #10b981);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}

/* Hero Actions */
.hero-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
    animation: slideInLeft 0.8s ease-out 1s both;
}

.btn-outline {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(5px);
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* Social Proof */
.hero-social-proof {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    animation: slideInLeft 0.8s ease-out 1.2s both;
}

.social-proof-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.social-proof-logos {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.logo-item {
    padding: 0.8rem 1.8rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    border-radius: 10px;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.logo-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* Hero Visual - Profile Image */
.hero-visual {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeInRight 1s ease-out 0.5s both;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.profile-container {
    position: relative;
    width: 400px;
    height: 400px;
}

.profile-image-wrapper {
    position: relative;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    overflow: hidden;
    animation: floatProfile 6s ease-in-out infinite;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 0 0 10px rgba(37, 99, 235, 0.3),
        0 0 0 20px rgba(14, 165, 233, 0.2);
    border: 5px solid white;
}

@keyframes floatProfile {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
    }
    25% {
        transform: translateY(-20px) rotate(2deg);
        border-radius: 50% 40% 60% 50% / 50% 50% 50% 50%;
    }
    50% {
        transform: translateY(0) rotate(0deg);
        border-radius: 40% 60% 40% 60% / 40% 40% 60% 60%;
    }
    75% {
        transform: translateY(10px) rotate(-2deg);
        border-radius: 60% 40% 40% 60% / 60% 60% 40% 40%;
    }
}

.hero-profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.profile-image-wrapper:hover .hero-profile-img {
    transform: scale(1.1);
}

.profile-frame {
    position: absolute;
    top: -20px;
    left: -20px;
    right: -20px;
    bottom: -20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: inherit;
    animation: floatProfile 6s ease-in-out infinite reverse;
}

.profile-badge {
    position: absolute;
    bottom: 30px;
    right: -10px;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    color: white;
    padding: 0.7rem 1.2rem;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    animation: bounce 2s infinite;
    z-index: 3;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-15px);
    }
}

/* Floating Elements */
.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.float-element {
    position: absolute;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    animation: floatElement 20s infinite linear;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.element-1 {
    top: 10%;
    left: 5%;
    animation-delay: 0s;
    background: rgba(59, 130, 246, 0.3);
}

.element-2 {
    top: 70%;
    right: 5%;
    animation-delay: -7s;
    animation-duration: 25s;
    background: rgba(16, 185, 129, 0.3);
}

.element-3 {
    bottom: 10%;
    left: 20%;
    animation-delay: -14s;
    animation-duration: 30s;
    background: rgba(239, 68, 68, 0.3);
}

@keyframes floatElement {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(20px, -20px) rotate(90deg);
    }
    50% {
        transform: translate(0, -40px) rotate(180deg);
    }
    75% {
        transform: translate(-20px, -20px) rotate(270deg);
    }
}

/* Scroll Indicator */
.scroll-indicator {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    color: rgba(255, 255, 255, 0.8);
    animation: fadeInUp 2s infinite;
    z-index: 2;
}

@keyframes fadeInUp {
    0%, 100% {
        opacity: 0.7;
        transform: translateX(-50%) translateY(0);
    }
    50% {
        opacity: 1;
        transform: translateX(-50%) translateY(-10px);
    }
}

.mouse {
    width: 30px;
    height: 50px;
    border: 2px solid rgba(255, 255, 255, 0.6);
    border-radius: 20px;
    margin-bottom: 0.5rem;
    position: relative;
    backdrop-filter: blur(5px);
}

.wheel {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 10px;
    background: white;
    border-radius: 2px;
    animation: scroll 2s infinite;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

@keyframes scroll {
    0% {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
    }
    100% {
        transform: translateX(-50%) translateY(20px);
        opacity: 0;
    }
}

.scroll-text {
    font-size: 0.9rem;
    font-weight: 500;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 3rem;
    }
    
    .hero-description {
        margin: 0 auto 2rem;
    }
    
    .hero-stats {
        justify-content: center;
    }
    
    .hero-actions {
        justify-content: center;
    }
    
    .social-proof-logos {
        justify-content: center;
    }
    
    .profile-container {
        margin: 0 auto;
    }
    
    .name {
        font-size: 3rem;
    }
    
    .hero-subtitle {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .hero {
        padding-top: 100px;
        min-height: 90vh;
    }
    
    .name {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.3rem;
    }
    
    .hero-stats {
        gap: 1.5rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .profile-container {
        width: 300px;
        height: 300px;
    }
    
    .profile-image-wrapper {
        width: 250px;
        height: 250px;
    }
    
    .float-element {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .hero {
        min-height: 85vh;
    }
    
    .name {
        font-size: 2rem;
    }
    
    .greeting {
        font-size: 1.2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .hero-stats {
        gap: 1rem;
    }
    
    .stat-item {
        min-width: 80px;
        padding: 0.8rem;
    }
    
    .profile-container {
        width: 250px;
        height: 250px;
    }
    
    .profile-image-wrapper {
        width: 200px;
        height: 200px;
    }
    
    .profile-badge {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        right: 0;
    }
    
    .logo-item {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}

.hero {
    /* Fallback gradient if image doesn't load */
    background: 
        url('assets/images/hero-bg.jpg') no-repeat center center/cover;
}

/* Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    z-index: 2000;
}

.modal-overlay.active {
    display: flex;
}

.modal-card {
    width: min(900px, 100%);
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    transform: translateY(10px);
    opacity: 0;
    transition: transform 0.2s ease, opacity 0.2s ease;
}

.modal-overlay.active .modal-card {
    transform: translateY(0);
    opacity: 1;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.25rem 0.75rem;
    border-bottom: 1px solid var(--gray-light);
}

.modal-title {
    font-size: 1.25rem;
}

.modal-close {
    width: 42px;
    height: 42px;
    border-radius: 999px;
    border: 1px solid var(--gray-light);
    background: white;
    color: var(--dark);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}

.modal-close:hover {
    transform: translateY(-1px);
    border-color: rgba(37, 99, 235, 0.35);
    background: rgba(37, 99, 235, 0.05);
}

.modal-body {
    padding: 1.25rem;
}

.modal-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

.mini-card {
    border: 1px solid var(--gray-light);
    border-radius: var(--radius);
    padding: 1rem;
    background: white;
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.mini-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: rgba(37, 99, 235, 0.25);
}

.mini-card-title {
    font-family: var(--font-heading);
    font-size: 1rem;
    margin-bottom: 0.35rem;
}

.mini-card-desc {
    color: var(--gray);
    font-size: 0.95rem;
    margin-bottom: 0.85rem;
}

.mini-card-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 0.55rem 1.1rem;
    font-size: 0.95rem;
}

.project-card.flash {
    outline: 3px solid rgba(37, 99, 235, 0.35);
    box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.12);
}

.mini-link {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
}

.mini-link i {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary);
}

.mini-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    background: rgba(16, 185, 129, 0.12);
    color: var(--accent);
    font-size: 0.8rem;
    font-weight: 600;
}

.mini-badge.warning {
    background: rgba(245, 158, 11, 0.12);
    color: var(--warning);
}

.top-header.scrolled {
    background: rgba(255, 255, 255, 0.86);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.9);
}

.top-header.scrolled .header-content {
    padding: 0.75rem 0;
}

.scroll-indicator {
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

@media (max-width: 768px) {
    .modal-grid {
        grid-template-columns: 1fr;
    }
}
    </style>
</head>
<body>
    <!-- Mobile Navigation Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar for Mobile -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="close-sidebar" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
            <div class="sidebar-profile">
                <img src="assets/images/njabulo-profile.jpg" alt="Njabulo Mavuso" class="profile-image" onerror="this.src='https://via.placeholder.com/100'">
                <h2>Njabulo Mavuso</h2>
                <p>CSPro Programmer</p>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="#home" class="nav-link"><i class="fas fa-home"></i> Home</a>
            <a href="#about" class="nav-link"><i class="fas fa-user"></i> About</a>
            <a href="#services" class="nav-link"><i class="fas fa-concierge-bell"></i> Services</a>
            <a href="#experience" class="nav-link"><i class="fas fa-briefcase"></i> Experience</a>
            <a href="#skills" class="nav-link"><i class="fas fa-code"></i> Skills</a>
            <a href="#projects" class="nav-link"><i class="fas fa-project-diagram"></i> Projects</a>
            <a href="#references" class="nav-link"><i class="fas fa-users"></i> References</a>
            <a href="#contact" class="nav-link"><i class="fas fa-envelope"></i> Contact</a>
            <a href="#cv" class="nav-link download-cv"><i class="fas fa-download"></i> Download CV</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation for Desktop -->
        <header class="top-header">
            <div class="container">
                <div class="header-content">
                    <div class="logo">
                        <img src="assets/images/njabulo-logo.png" alt="Njabulo Mavuso Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/50'">
                        <div class="logo-text">
                            <h1>Njabulo Mavuso</h1>
                            <span class="tagline">CSPro Programmer & IT Officer</span>
                        </div>
                    </div>
                    <nav class="desktop-nav">
                        <a href="#home">Home</a>
                        <a href="#about">About</a>
                        <a href="#services">Services</a>
                        <a href="#experience">Experience</a>
                        <a href="#skills">Skills</a>
                        <a href="#projects">Projects</a>
                        <a href="#references">References</a>
                        <a href="#contact">Contact</a>
                        <a href="#cv" class="btn-nav">Download CV</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="hero">
            <div class="hero-background"><div class="hero-overlay"></div></div>
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <div class="hero-badge">
                            <span class="badge-text">Welcome to My Portfolio</span>
                            <div class="badge-pulse"></div>
                        </div>
                        <h1 class="hero-title">
                            <span class="greeting">Hello, I'm</span>
                            <span class="name">Njabulo Mavuso</span>
                        </h1>
                        <h2 class="hero-subtitle">
                            I'm a <span class="typed-text" id="typed-text"></span><span class="typed-cursor">|</span>
                        </h2>
                        <p class="hero-description">
                            Specializing in Population & Housing Census (PHC) applications using CSPro and CSWeb.
                            Building scalable solutions for national data collection systems with 2+ years of experience.
                        </p>
                        <div class="hero-stats">
                            <div class="stat-item"><div class="stat-number" data-count="50">0</div><div class="stat-label">Projects Completed</div></div>
                            <div class="stat-item"><div class="stat-number" data-count="2">0</div><div class="stat-label">Years Experience</div></div>
                            <div class="stat-item"><div class="stat-number" data-count="100">0</div><div class="stat-label">% Client Satisfaction</div></div>
                        </div>
                        <div class="hero-actions">
                            <button type="button" class="btn btn-primary" id="openHireMe"><i class="fas fa-paper-plane"></i> Hire Me</button>
                            <button type="button" class="btn btn-secondary" id="openProjects"><i class="fas fa-code"></i> View Projects</button>
                            <a href="#cv" class="btn btn-outline"><i class="fas fa-download"></i> Download CV</a>
                        </div>
                        <div class="hero-social-proof">
                            <div class="social-proof-text">Trusted by organizations like:</div>
                            <div class="social-proof-logos">
                                <div class="logo-item">CSO</div><div class="logo-item">Ministry</div><div class="logo-item">Limkokwing</div>
                            </div>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <div class="profile-container">
                            <div class="profile-image-wrapper">
                                <img src="assets/images/njabulo-hero.jpg" alt="Njabulo Mavuso" class="hero-profile-img" onerror="this.src='https://via.placeholder.com/350'">
                                <div class="profile-badge"><i class="fas fa-award"></i><span>CSPro Expert</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="scroll-indicator" href="#about" aria-label="Scroll to About section"><div class="mouse"><div class="wheel"></div></div><span class="scroll-text">Scroll to explore</span></a>
        </section>

        <!-- About Section -->
        <section id="about" class="section about-section">
            <div class="container">
                <h2 class="section-title">About Me</h2>
                <div class="about-content">
                    <div class="about-text">
                        <p class="intro">IT professional with an Associate Degree in Information Technology from <strong>Limkokwing University of Creative Technology</strong>. Currently contributing to national-scale data collection systems at the Central Statistical Office.</p>
                        <div class="about-points">
                            <div class="point"><i class="fas fa-graduation-cap"></i><div><h3>Education</h3><p>Associate Degree in IT, Limkokwing University</p></div></div>
                            <div class="point"><i class="fas fa-building"></i><div><h3>Current Role</h3><p>IT Officer & CSPro Programmer at CSO</p></div></div>
                            <div class="point"><i class="fas fa-project-diagram"></i><div><h3>Specialization</h3><p>Population & Housing Census Applications</p></div></div>
                        </div>
                        <div class="journey"><h3>My Professional Journey</h3><p>Started as an IT intern at the Ministry of Economic Planning and Development, where I developed the <strong>Visitor Management System</strong> from concept to implementation. Progressed to System Administrator for the PIMS system before joining the Central Statistical Office to work on the PHC project.</p></div>
                    </div>
                    <div class="about-image">
                        <div class="image-container">
                            <img src="assets/images/njabulo-about.jpg" alt="Njabulo Mavuso" class="profile-photo" onerror="this.src='https://via.placeholder.com/350'">
                            <div class="experience-badge"><span class="exp-years">2+</span> <span class="exp-text">Years Experience</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="section services-section">
            <div class="container">
                <h2 class="section-title">What I Offer</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-database"></i></div>
                        <h3>Data Systems Development</h3>
                        <p>Specialized in building national-scale data collection systems using CSPro and CSWeb, ensuring data integrity and real-time synchronization.</p>
                        <ul class="service-list">
                            <li><i class="fas fa-check"></i> Census Application Design</li>
                            <li><i class="fas fa-check"></i> CSWeb Dashboard Setup</li>
                            <li><i class="fas fa-check"></i> Data Quality Assurance</li>
                        </ul>
                    </div>
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-laptop-code"></i></div>
                        <h3>Full-Stack Web Solutions</h3>
                        <p>Creating responsive, modern web applications with PHP, JavaScript, and modern CSS frameworks tailored for business efficiency.</p>
                        <ul class="service-list">
                            <li><i class="fas fa-check"></i> Custom Admin Dashboards</li>
                            <li><i class="fas fa-check"></i> API Integration</li>
                            <li><i class="fas fa-check"></i> Database Optimization</li>
                        </ul>
                    </div>
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-server"></i></div>
                        <h3>System Administration</h3>
                        <p>Managing IT infrastructure, network configurations, and server deployments to ensure 99.9% uptime and security for organizational systems.</p>
                        <ul class="service-list">
                            <li><i class="fas fa-check"></i> Server Maintenance</li>
                            <li><i class="fas fa-check"></i> Network Security</li>
                            <li><i class="fas fa-check"></i> Technical Support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Experience Timeline -->
        <section id="experience" class="section experience-section">
            <div class="container">
                <h2 class="section-title">Professional Experience</h2>
                <div class="timeline">
                    <div class="timeline-item bounce-card">
                        <div class="timeline-date">Present</div>
                        <div class="timeline-content">
                            <div class="timeline-header"><h3>IT Officer & CSPro Programmer</h3><span class="company">Central Statistical Office (CSO)</span></div>
                            <p class="timeline-description">Developing applications for the Population & Housing Census (PHC) project using CSPro and CSWeb. Creating scalable data collection systems and dashboards for national census operations.</p>
                            <div class="timeline-tags"><span class="tag">CSPro</span><span class="tag">CSWeb</span><span class="tag">Data Collection</span><span class="tag">System Development</span></div>
                        </div>
                    </div>
                    <div class="timeline-item bounce-card">
                        <div class="timeline-date">2024-2025</div>
                        <div class="timeline-content">
                            <div class="timeline-header"><h3>System Administrator & Developer</h3><span class="company">Ministry of Economic Planning</span></div>
                            <p class="timeline-description">Pitched and developed the Visitor Management System. Administered the PIMS system. Managed network configuration and IT infrastructure.</p>
                            <div class="timeline-tags"><span class="tag">System Administration</span><span class="tag">PHP</span><span class="tag">Networking</span><span class="tag">PIMS</span></div>
                        </div>
                    </div>
                    <div class="timeline-item bounce-card">
                        <div class="timeline-date">2024-2024</div>
                        <div class="timeline-content">
                            <div class="timeline-header"><h3>IT Intern</h3><span class="company">Ministry of Economic Planning & Development</span></div>
                            <p class="timeline-description">Started as IT personnel, developed technical skills in networking and system configuration. Created the first prototype of the Visitor Management System.</p>
                            <div class="timeline-tags"><span class="tag">Internship</span><span class="tag">IT Support</span><span class="tag">System Configuration</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Hire Me Section -->
        <section class="section why-hire-section">
            <div class="container">
                <div class="why-hire-content">
                    <div class="why-hire-text">
                        <h2 class="section-title" style="text-align: left; margin-bottom: 2rem;">Why Hire Me?</h2>
                        <p class="intro">I don't just write code; I build solutions that solve real-world problems. My experience at the Central Statistical Office and the Ministry of Economic Planning has taught me how to handle high-stakes data and critical infrastructure.</p>
                        
                        <div class="value-propositions">
                            <div class="value-item">
                                <div class="value-icon"><i class="fas fa-rocket"></i></div>
                                <div class="value-info">
                                    <h3>Efficiency Focused</h3>
                                    <p>I specialize in automating manual processes, like the Visitor Management System I developed for the Ministry, saving hundreds of man-hours.</p>
                                </div>
                            </div>
                            <div class="value-item">
                                <div class="value-icon"><i class="fas fa-shield-alt"></i></div>
                                <div class="value-info">
                                    <h3>Data Integrity</h3>
                                    <p>With experience in national census projects, I understand the importance of data security and accuracy at scale.</p>
                                </div>
                            </div>
                            <div class="value-item">
                                <div class="value-icon"><i class="fas fa-sync"></i></div>
                                <div class="value-info">
                                    <h3>Adaptability</h3>
                                    <p>From CSPro for census to PHP/JS for web systems, I quickly master the tools needed for the job.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="why-hire-stats">
                        <div class="hire-stat-card">
                            <div class="hire-stat-number">2+</div>
                            <div class="hire-stat-label">Years of Pro Experience</div>
                        </div>
                        <div class="hire-stat-card">
                            <div class="hire-stat-number">3</div>
                            <div class="hire-stat-label">Core Strengths</div>
                        </div>
                        <div class="hire-stat-card">
                            <div class="hire-stat-number">100%</div>
                            <div class="hire-stat-label">Commitment to Quality</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Skills Section -->
        <section id="skills" class="section skills-section">
            <div class="container">
                <h2 class="section-title">Technical Skills</h2>
                <div class="skills-grid">
                    <div class="skill-category">
                        <h3 class="category-title">Programming & Development</h3>
                        <div class="skill-list">
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">CSPro / CSWeb</span><span class="skill-percent">95%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="95"></div></div>
                            </div>
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">PHP Development</span><span class="skill-percent">90%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="90"></div></div>
                            </div>
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">JavaScript</span><span class="skill-percent">85%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="85"></div></div>
                            </div>
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">HTML/CSS</span><span class="skill-percent">92%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="92"></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="skill-category">
                        <h3 class="category-title">Systems & Administration</h3>
                        <div class="skill-list">
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">System Administration</span><span class="skill-percent">88%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="88"></div></div>
                            </div>
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">Network Configuration</span><span class="skill-percent">85%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="85"></div></div>
                            </div>
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">Visitor Management Systems</span><span class="skill-percent">90%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="90"></div></div>
                            </div>
                            <div class="skill">
                                <div class="skill-header"><span class="skill-name">Database Management</span><span class="skill-percent">87%</span></div>
                                <div class="skill-bar"><div class="skill-level" data-level="87"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section id="projects" class="section projects-section">
            <div class="container">
                <h2 class="section-title">Featured Projects</h2>
                <div class="projects-grid">
                    <div class="project-card in-progress">
                        <div class="project-icon"><i class="fas fa-boxes"></i></div>
                        <div class="status-badge">In Progress</div>
                        <h3 class="project-title">EBIS Inventory System</h3>
                        <p class="project-description">A robust inventory management system designed to track assets, manage stock levels, and generate comprehensive reports for business operations.</p>
                        <div class="project-tech"><span>PHP</span><span>MySQL</span><span>Bootstrap</span><span>Inventory Control</span></div>
                    </div>
                    <div class="project-card in-progress">
                        <div class="project-icon"><i class="fas fa-vote-yea"></i></div>
                        <div class="status-badge">In Progress</div>
                        <h3 class="project-title">Online Voting System</h3>
                        <p class="project-description">A secure and transparent digital voting platform featuring voter authentication, real-time results, and encrypted ballot management.</p>
                        <div class="project-tech"><span>PHP</span><span>JavaScript</span><span>Security</span><span>Data Integrity</span></div>
                    </div>
                    <div class="project-card">
                        <div class="project-icon"><i class="fas fa-chart-line"></i></div>
                        <h3 class="project-title">PHC Dashboard</h3>
                        <p class="project-description">Population & Housing Census dashboard for real-time data visualization and analytics. Built for the Central Statistical Office.</p>
                        <div class="project-links"><a href="https://sonofall.great-site.net/phc_dashboard/" target="_blank" class="project-link"><i class="fas fa-external-link-alt"></i> View Live</a></div>
                        <div class="project-tech"><span>PHP</span><span>JavaScript</span><span>Chart.js</span></div>
                    </div>
                    <div class="project-card">
                        <div class="project-icon"><i class="fas fa-school"></i></div>
                        <h3 class="project-title">SmartSchoolHubsz</h3>
                        <p class="project-description">Announcement application for educational institutions with real-time notifications. Developed during university.</p>
                        <div class="project-links"><a href="https://smartschoolhubsz.netlify.app" target="_blank" class="project-link"><i class="fas fa-external-link-alt"></i> View Live</a></div>
                        <div class="project-tech"><span>JavaScript</span><span>Netlify</span><span>Firebase</span></div>
                    </div>
                    <div class="project-card">
                        <div class="project-icon"><i class="fas fa-id-card"></i></div>
                        <h3 class="project-title">Visitor Management System</h3>
                        <p class="project-description">Complete visitor registration and tracking system for the Ministry of Economic Planning. From concept to implementation.</p>
                        <div class="project-tech"><span>PHP</span><span>MySQL</span><span>QR Codes</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- References Section -->
        <section id="references" class="section references-section">
            <div class="container">
                <h2 class="section-title">References</h2>
                <div class="references-container">
                    <div class="reference-selector">
                        <div class="reference-thumbnails">
                            <div class="thumbnail-item active" data-ref="0"><div class="thumbnail-image"><img src="assets/images/ref-ngozo.jpg" alt="Mr. T. Ngozo" onerror="this.src='https://via.placeholder.com/100'"></div><span class="thumbnail-name">Mr. T. Ngozo</span></div>
                            <div class="thumbnail-item" data-ref="1"><div class="thumbnail-image"><img src="assets/images/ref-simelane.jpg" alt="Mr. T. Simelane" onerror="this.src='https://via.placeholder.com/100'"></div><span class="thumbnail-name">Mr. T. Simelane</span></div>
                            <div class="thumbnail-item" data-ref="2"><div class="thumbnail-image"><img src="assets/images/ref-masilela.jpg" alt="Mr. S.D. Masilela" onerror="this.src='https://via.placeholder.com/100'"></div><span class="thumbnail-name">Mr. S.D. Masilela</span></div>
                        </div>
                    </div>
                    <div class="reference-cards">
                        <div class="reference-card active" id="ref-0">
                            <div class="reference-header"><div class="reference-image"><img src="assets/images/ref-ngozo.jpg" alt="" onerror="this.src='https://via.placeholder.com/80'"></div><div class="reference-title"><h3>Mr. T. Ngozo</h3><h4>Senior Programmer</h4></div></div>
                            <div class="reference-details"><div class="detail-item"><i class="fas fa-briefcase"></i><div><strong>Position:</strong> Senior Programmer</div></div><div class="detail-item"><i class="fas fa-building"></i><div><strong>Organization:</strong> Limkokwing University</div></div><div class="detail-item"><i class="fas fa-map-marker-alt"></i><div><strong>Campus:</strong> Mbabane Campus</div></div><div class="detail-item"><i class="fas fa-phone"></i><div><strong>Phone:</strong> 268 79866017</div></div></div>
                            <div class="reference-quote"><i class="fas fa-quote-left"></i><p>Njabulo demonstrated exceptional programming skills during his studies and showed great potential in application development.</p></div>
                        </div>
                        <div class="reference-card" id="ref-1">
                            <div class="reference-header"><div class="reference-image"><img src="assets/images/ref-simelane.jpg" alt="" onerror="this.src='https://via.placeholder.com/80'"></div><div class="reference-title"><h3>Mr. T. Simelane</h3><h4>Computer Systems Engineer</h4></div></div>
                            <div class="reference-details"><div class="detail-item"><i class="fas fa-briefcase"></i><div><strong>Position:</strong> Head of Academics</div></div><div class="detail-item"><i class="fas fa-building"></i><div><strong>Organization:</strong> Limkokwing University</div></div><div class="detail-item"><i class="fas fa-map-marker-alt"></i><div><strong>Campus:</strong> Mbabane Campus</div></div><div class="detail-item"><i class="fas fa-phone"></i><div><strong>Phone:</strong> 268 76235694</div></div></div>
                            <div class="reference-quote"><i class="fas fa-quote-left"></i><p>Njabulo was an outstanding student who consistently delivered high-quality projects and showed strong problem-solving abilities.</p></div>
                        </div>
                        <div class="reference-card" id="ref-2">
                            <div class="reference-header"><div class="reference-image"><img src="assets/images/ref-masilela.jpg" alt="" onerror="this.src='https://via.placeholder.com/80'"></div><div class="reference-title"><h3>Mr. S.D. Masilela</h3><h4>Under Secretary</h4></div></div>
                            <div class="reference-details"><div class="detail-item"><i class="fas fa-briefcase"></i><div><strong>Position:</strong> Under Secretary</div></div><div class="detail-item"><i class="fas fa-building"></i><div><strong>Organization:</strong> Ministry of Economic Planning & Development</div></div><div class="detail-item"><i class="fas fa-phone"></i><div><strong>Phone:</strong> 268 7606 3513</div></div></div>
                            <div class="reference-quote"><i class="fas fa-quote-left"></i><p>During his internship, Njabulo showed remarkable initiative in developing the Visitor Management System, which significantly improved our operations.</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="section contact-section">
            <div class="container">
                <h2 class="section-title">Get In Touch</h2>
                <div class="contact-content">
                    <div class="contact-info">
                        <h3 class="contact-subtitle">Connect With Me</h3>
                        <p class="contact-description">Interested in collaborating on census technology or data systems? Reach out through any platform below.</p>
                        <div class="contact-methods">
                            <a href="mailto:njabulob.mavuso@gmail.com" class="contact-method"><i class="fas fa-envelope"></i><div><h4>Email</h4><p>njabulob.mavuso@gmail.com</p></div></a>
                            <a href="https://www.linkedin.com/in/njabulob-mavuso-95b677256" target="_blank" class="contact-method"><i class="fab fa-linkedin"></i><div><h4>LinkedIn</h4><p>Njabulo Mavuso</p></div></a>
                            <a href="https://github.com/son0fall" target="_blank" class="contact-method"><i class="fab fa-github"></i><div><h4>GitHub</h4><p>son0fall</p></div></a>
                            <a href="https://wa.me/26879792770" target="_blank" class="contact-method"><i class="fab fa-whatsapp"></i><div><h4>WhatsApp</h4><p>+268 7979 2770</p></div></a>
                        </div>
                    </div>
                    <div class="contact-form">
                        <form id="contactForm" action="contact-process.php" method="POST">
                            <div class="form-group"><input type="text" name="name" placeholder="Your Name" required></div>
                            <div class="form-group"><input type="email" name="email" placeholder="Your Email" required></div>
                            <div class="form-group"><input type="text" name="subject" placeholder="Subject"></div>
                            <div class="form-group"><textarea name="message" placeholder="Your Message" rows="5" required></textarea></div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Message</button>
                            <div id="formMessage" class="form-message"></div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- CV Download Section -->
        <section id="cv" class="section cv-section">
            <div class="container">
                <h2 class="section-title">Download My CV</h2>
                <p class="section-subtitle">ATS-friendly resume for hiring managers</p>
                <div class="cv-options">
                    <a href="assets/cv-njabulo-mavuso.pdf" download class="cv-option"><div class="cv-icon pdf"><i class="fas fa-file-pdf"></i></div><div class="cv-info"><h3>PDF Format</h3><p>Optimized for online applications</p><span class="file-size">~250 KB</span></div><div class="cv-action"><i class="fas fa-download"></i></div></a>
                    <a href="assets/cv-njabulo-mavuso.docx" download class="cv-option"><div class="cv-icon word"><i class="fas fa-file-word"></i></div><div class="cv-info"><h3>Word Format</h3><p>Editable version for customization</p><span class="file-size">~180 KB</span></div><div class="cv-action"><i class="fas fa-download"></i></div></a>
                </div>
                <div class="cv-tips"><h4><i class="fas fa-lightbulb"></i> ATS Optimization Tips</h4><ul><li>Keywords: CSPro, PHP, System Administration, IT Officer</li><li>Clear section headers: Experience, Education, Skills</li><li>Standard formatting for applicant tracking systems</li><li>Both formats provided for different requirements</li></ul></div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-about"><div class="footer-logo"><img src="assets/images/njabulo-logo.png" alt="Njabulo Mavuso" onerror="this.src='https://via.placeholder.com/60'"></div><div><h3>Njabulo Mavuso</h3><p>CSPro Programmer & IT Officer specializing in census technology and data systems.</p></div></div>
                    <div class="footer-links">
                        <div class="link-group"><h4>Quick Links</h4><a href="#home">Home</a><a href="#about">About</a><a href="#services">Services</a><a href="#experience">Experience</a><a href="#projects">Projects</a><a href="#references">References</a></div>
                        <div class="link-group"><h4>Connect</h4><a href="mailto:njabulob.mavuso@gmail.com">Email</a><a href="https://linkedin.com/in/njabulo-mavuso" target="_blank">LinkedIn</a><a href="https://github.com/son0fall" target="_blank">GitHub</a><a href="https://wa.me/26879792770" target="_blank">WhatsApp</a></div>
                        <div class="link-group"><h4>Projects</h4><a href="https://sonofall.great-site.net/phc_dashboard/" target="_blank">PHC Dashboard</a><a href="https://smartschoolhubsz.netlify.app" target="_blank">SmartSchoolHubsz</a><a href="https://sonofall.netlify.app" target="_blank">Portfolio</a></div>
                    </div>
                    <?php if (isset($_GET['admin']) && $_GET['admin'] == 'njabulo2024'): ?>
                    <div class="visitor-stats">
                        <h4><i class="fas fa-chart-bar"></i> Visitor Statistics</h4>
                        <?php if (file_exists($visitorFile)) { $data = json_decode(file_get_contents($visitorFile), true); echo "<p>Total Visitors: <strong>" . $data['total'] . "</strong></p><p>Today: <strong>" . ($data['daily'][$today] ?? 0) . "</strong></p><p>Last Updated: " . ($data['last_updated'] ?? 'N/A') . "</p>"; } ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="footer-bottom">
                    <div class="footer-social-icons">
                        <a class="linkedin" href="https://www.linkedin.com/in/njabulob-mavuso-95b677256" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a class="github" href="https://github.com/son0fall" target="_blank" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        <a class="whatsapp" href="https://wa.me/26879792770" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a class="email" href="mailto:njabulob.mavuso@gmail.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                    <div class="footer-copyright">
                        <p>&copy; <span id="currentYear"><?php echo date('Y'); ?></span> Njabulo Mavuso. All rights reserved.</p>
                    </div>
                    <div class="footer-datetime">
                        <p><?php echo date('Y-m-d H:i'); ?></p>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <div class="modal-overlay" id="quickModal" aria-hidden="true">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="quickModalTitle">
            <div class="modal-header">
                <h3 class="modal-title" id="quickModalTitle"></h3>
                <button type="button" class="modal-close" id="quickModalClose" aria-label="Close dialog"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="quickModalBody"></div>
        </div>
    </div>

    <!-- Chatbot Widget -->
    <div class="chatbot-widget" id="chatbotWidget">
        <div class="chatbot-container" id="chatbotContainer">
            <div class="chatbot-header"><h3><i class="fas fa-robot"></i> Ask About Me</h3><button class="chatbot-close" id="chatbotClose"><i class="fas fa-times"></i></button></div>
            <div class="chatbot-body">
                <div class="chatbot-messages" id="chatbotMessages"><div class="message bot">Hello! I can tell you about Njabulo's skills, experience, projects, and how to contact him. What would you like to know?</div></div>
                <div class="chatbot-input"><input type="text" id="chatbotInput" placeholder="Ask a question..."><button id="chatbotSend"><i class="fas fa-paper-plane"></i></button></div>
                <div class="chatbot-suggestions"><span>Quick questions:</span><button class="suggestion-btn" data-question="What is your experience?">Experience</button><button class="suggestion-btn" data-question="What are your skills?">Skills</button><button class="suggestion-btn" data-question="How to contact you?">Contact</button></div>
            </div>
        </div>
        <button class="chatbot-toggle" id="chatbotToggle"><i class="fas fa-robot"></i><span class="notification-dot"></span></button>
    </div>

    <!-- Typed.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <!-- Main JavaScript (integrated) -->
    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                // ----- TYPED.JS -----
                if (document.getElementById('typed-text')) {
                    new Typed('#typed-text', {
                        strings: ['CSPro Programmer', 'IT Officer', 'System Administrator', 'PHC Specialist', 'Web Developer'],
                        typeSpeed: 50, backSpeed: 30, backDelay: 2000, loop: true, showCursor: false
                    });
                }

                // ----- MOBILE MENU -----
                const toggleBtn = document.getElementById('mobileMenuToggle');
                const sidebar = document.getElementById('sidebar');
                const closeBtn = document.getElementById('closeSidebar');
                if (toggleBtn && sidebar && closeBtn) {
                    toggleBtn.addEventListener('click', () => sidebar.classList.add('active'));
                    closeBtn.addEventListener('click', () => sidebar.classList.remove('active'));
                    document.querySelectorAll('.sidebar-nav a').forEach(link => {
                        link.addEventListener('click', () => sidebar.classList.remove('active'));
                    });
                }

                // ----- ACTIVE NAV LINK ON SCROLL -----
                const sections = document.querySelectorAll('section[id]');
                const navLinks = document.querySelectorAll('.desktop-nav a, .sidebar-nav a');
                function setActiveLink() {
                    let scrollY = window.scrollY + 150;
                    sections.forEach(sec => {
                        const top = sec.offsetTop;
                        const height = sec.offsetHeight;
                        const id = sec.getAttribute('id');
                        if (scrollY >= top && scrollY < top + height) {
                            navLinks.forEach(link => {
                                link.classList.remove('active');
                                if (link.getAttribute('href') === '#' + id) link.classList.add('active');
                            });
                        }
                    });
                }
                window.addEventListener('scroll', setActiveLink);
                setActiveLink();

                // ----- SMOOTH SCROLL -----
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) target.scrollIntoView({ behavior: 'smooth' });
                    });
                });

                // ----- HEADER (DYNAMIC) -----
                const topHeader = document.querySelector('.top-header');
                function setHeaderState() {
                    if (!topHeader) return;
                    if (window.scrollY > 10) topHeader.classList.add('scrolled');
                    else topHeader.classList.remove('scrolled');
                }
                window.addEventListener('scroll', setHeaderState, { passive: true });
                setHeaderState();

                // ----- QUICK MODALS (HIRE ME + PROJECTS) -----
                const quickModal = document.getElementById('quickModal');
                const quickModalTitle = document.getElementById('quickModalTitle');
                const quickModalBody = document.getElementById('quickModalBody');
                const quickModalClose = document.getElementById('quickModalClose');
                const openHireMe = document.getElementById('openHireMe');
                const openProjects = document.getElementById('openProjects');

                function closeQuickModal() {
                    if (!quickModal) return;
                    quickModal.classList.remove('active');
                    quickModal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }

                function openQuickModal(title, bodyNode) {
                    if (!quickModal || !quickModalTitle || !quickModalBody) return;
                    quickModalTitle.textContent = title;
                    quickModalBody.innerHTML = '';
                    quickModalBody.appendChild(bodyNode);
                    quickModal.classList.add('active');
                    quickModal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }

                function createContactGrid() {
                    const grid = document.createElement('div');
                    grid.className = 'modal-grid';

                    const methods = document.querySelectorAll('.contact-methods .contact-method');
                    if (!methods.length) {
                        const fallback = document.createElement('div');
                        fallback.textContent = 'Contact details are not available right now.';
                        return fallback;
                    }

                    methods.forEach(method => {
                        const href = method.getAttribute('href');
                        const target = method.getAttribute('target');
                        const icon = method.querySelector('i');
                        const title = method.querySelector('h4');
                        const subtitle = method.querySelector('p');

                        const card = document.createElement('div');
                        card.className = 'mini-card';

                        const link = document.createElement('a');
                        link.className = 'mini-link';
                        link.href = href || '#';
                        if (target) link.target = target;
                        link.rel = target === '_blank' ? 'noopener noreferrer' : '';

                        const iconWrap = document.createElement('i');
                        if (icon) iconWrap.className = icon.className;
                        else iconWrap.className = 'fas fa-link';

                        const textWrap = document.createElement('div');
                        const t = document.createElement('div');
                        t.className = 'mini-card-title';
                        t.textContent = title ? title.textContent : 'Contact';
                        const d = document.createElement('div');
                        d.className = 'mini-card-desc';
                        d.textContent = subtitle ? subtitle.textContent : '';
                        textWrap.appendChild(t);
                        textWrap.appendChild(d);

                        link.prepend(iconWrap);
                        link.appendChild(textWrap);
                        card.appendChild(link);
                        grid.appendChild(card);
                    });

                    return grid;
                }

                function createProjectsGrid() {
                    const grid = document.createElement('div');
                    grid.className = 'modal-grid';

                    const projectCards = document.querySelectorAll('.projects-section .project-card');
                    if (!projectCards.length) {
                        const fallback = document.createElement('div');
                        fallback.textContent = 'No projects found.';
                        return fallback;
                    }

                    projectCards.forEach((card, index) => {
                        card.dataset.projectIndex = String(index);

                        const title = card.querySelector('.project-title')?.textContent?.trim() || 'Project';
                        const desc = card.querySelector('.project-description')?.textContent?.trim() || '';
                        const status = card.querySelector('.status-badge')?.textContent?.trim() || '';

                        const mini = document.createElement('div');
                        mini.className = 'mini-card';

                        const t = document.createElement('div');
                        t.className = 'mini-card-title';
                        t.textContent = title;

                        const d = document.createElement('div');
                        d.className = 'mini-card-desc';
                        d.textContent = desc.length > 120 ? desc.slice(0, 117) + '...' : desc;

                        const actions = document.createElement('div');
                        actions.className = 'mini-card-actions';

                        if (status) {
                            const badge = document.createElement('span');
                            badge.className = 'mini-badge' + (status.toLowerCase().includes('progress') ? ' warning' : '');
                            badge.textContent = status;
                            actions.appendChild(badge);
                        }

                        const readMore = document.createElement('button');
                        readMore.type = 'button';
                        readMore.className = 'btn btn-primary btn-sm';
                        readMore.innerHTML = '<i class="fas fa-arrow-right"></i> Read More';
                        readMore.addEventListener('click', () => {
                            closeQuickModal();
                            const projectsSection = document.getElementById('projects');
                            if (projectsSection) projectsSection.scrollIntoView({ behavior: 'smooth' });
                            setTimeout(() => {
                                const targetCard = document.querySelector('.projects-section .project-card[data-project-index="' + index + '"]');
                                if (!targetCard) return;
                                targetCard.classList.add('flash');
                                targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                setTimeout(() => targetCard.classList.remove('flash'), 1400);
                            }, 350);
                        });
                        actions.appendChild(readMore);

                        mini.appendChild(t);
                        mini.appendChild(d);
                        mini.appendChild(actions);
                        grid.appendChild(mini);
                    });

                    return grid;
                }

                if (openHireMe) {
                    openHireMe.addEventListener('click', () => {
                        openQuickModal('Contact Me', createContactGrid());
                    });
                }

                if (openProjects) {
                    openProjects.addEventListener('click', () => {
                        openQuickModal('My Projects', createProjectsGrid());
                    });
                }

                if (quickModalClose) quickModalClose.addEventListener('click', closeQuickModal);
                if (quickModal) {
                    quickModal.addEventListener('click', (e) => {
                        if (e.target === quickModal) closeQuickModal();
                    });
                }
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && quickModal && quickModal.classList.contains('active')) closeQuickModal();
                });

                // ----- STATS COUNTER (intersection observer) -----
                const counters = document.querySelectorAll('.stat-number');
                const counterObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const counter = entry.target;
                            const target = +counter.getAttribute('data-count');
                            let count = 0;
                            const update = setInterval(() => {
                                count += Math.ceil(target / 50);
                                if (count >= target) { counter.innerText = target; clearInterval(update); }
                                else counter.innerText = count;
                            }, 20);
                            counterObserver.unobserve(counter);
                        }
                    });
                }, { threshold: 0.5 });
                counters.forEach(c => counterObserver.observe(c));

                // ----- SKILL BARS ANIMATION -----
                const skillLevels = document.querySelectorAll('.skill-level');
                const skillObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const bar = entry.target;
                            const level = bar.getAttribute('data-level');
                            bar.style.width = level + '%';
                            skillObserver.unobserve(bar);
                        }
                    });
                }, { threshold: 0.3 });
                skillLevels.forEach(bar => skillObserver.observe(bar));

                // ----- REFERENCE THUMBNAILS SWITCH -----
                const thumbItems = document.querySelectorAll('.thumbnail-item');
                const refCards = document.querySelectorAll('.reference-card');
                thumbItems.forEach(item => {
                    item.addEventListener('click', () => {
                        const refId = item.getAttribute('data-ref');
                        thumbItems.forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                        refCards.forEach(card => card.classList.remove('active'));
                        const activeCard = document.getElementById('ref-' + refId);
                        if (activeCard) activeCard.classList.add('active');
                    });
                });

                // ----- CHATBOT FUNCTIONALITY -----
                const chatToggle = document.getElementById('chatbotToggle');
                const chatContainer = document.getElementById('chatbotContainer');
                const chatClose = document.getElementById('chatbotClose');
                const chatSend = document.getElementById('chatbotSend');
                const chatInput = document.getElementById('chatbotInput');
                const chatMessages = document.getElementById('chatbotMessages');
                const suggestionBtns = document.querySelectorAll('.suggestion-btn');

                if (chatToggle && chatContainer) {
                    chatToggle.addEventListener('click', () => {
                        chatContainer.classList.toggle('active');
                        if (chatContainer.classList.contains('active')) chatToggle.style.display = 'none';
                        else chatToggle.style.display = 'flex';
                    });
                }
                if (chatClose) {
                    chatClose.addEventListener('click', () => {
                        chatContainer.classList.remove('active');
                        chatToggle.style.display = 'flex';
                    });
                }

                function addMessage(text, sender = 'user') {
                    if (!chatMessages) return;
                    const msgDiv = document.createElement('div');
                    msgDiv.classList.add('message', sender);
                    msgDiv.textContent = text;
                    chatMessages.appendChild(msgDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    return msgDiv;
                }

                async function botResponse(question) {
                    const typing = addMessage('Typing...', 'bot');
                    try {
                        const form = new FormData();
                        form.append('message', question);
                        const res = await fetch('chatbot-process.php', { method: 'POST', body: form });
                        const data = await res.json();
                        if (typing) typing.remove();
                        const text = (data && data.response) ? String(data.response) : "I couldn't generate a response right now.";
                        addMessage(text, 'bot');
                    } catch (e) {
                        if (typing) typing.remove();
                        addMessage('Network error. Please try again.', 'bot');
                    }
                }

                if (chatSend && chatInput) {
                    chatSend.addEventListener('click', () => {
                        const msg = chatInput.value.trim();
                        if (!msg) return;
                        addMessage(msg, 'user');
                        chatInput.value = '';
                        botResponse(msg);
                    });
                    chatInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') chatSend.click();
                    });
                }

                suggestionBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const question = btn.getAttribute('data-question');
                        addMessage(question, 'user');
                        botResponse(question);
                    });
                });

                // ----- UPDATE CURRENT YEAR -----
                const yearSpan = document.getElementById('currentYear');
                if (yearSpan) yearSpan.textContent = new Date().getFullYear();

                // ----- BOUNCE CARDS VISIBILITY -----
                const bounceCards = document.querySelectorAll('.bounce-card');
                const bounceObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) entry.target.classList.add('visible');
                    });
                }, { threshold: 0.2 });
                bounceCards.forEach(c => bounceObserver.observe(c));
            });
        })();


        // Inside your DOMContentLoaded event (or in a separate script tag)
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();  // stop normal form submission

        const formData = new FormData(contactForm);
        const messageDiv = document.getElementById('formMessage');

        try {
            const response = await fetch('contact-process.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            // Clear previous message classes
            messageDiv.className = 'form-message';
            if (result.success) {
                messageDiv.classList.add('success');
                messageDiv.textContent = result.message;
                contactForm.reset();  // optionally clear the form
            } else {
                messageDiv.classList.add('error');
                messageDiv.textContent = result.message;
            }
        } catch (error) {
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'Network error. Please try again.';
        }
    });
}
    </script>
    <script async src="https://cse.google.com/cse.js?cx=13045ac7eb7c0461d">
</script>
</body>
</html>
