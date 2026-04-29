document.addEventListener('DOMContentLoaded', function() {
    if (window.Typed && document.getElementById('typed-text')) {
        new Typed('#typed-text', {
            strings: ['CSPro Programmer', 'IT Officer', 'System Administrator', 'PHC Specialist', 'Web Developer'],
            typeSpeed: 50,
            backSpeed: 30,
            backDelay: 2000,
            loop: true,
            showCursor: false
        });
    }

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

    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.desktop-nav a, .sidebar-nav a');
    function setActiveLink() {
        const scrollY = window.scrollY + 150;
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
    window.addEventListener('scroll', setActiveLink, { passive: true });
    setActiveLink();

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return;
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    const topHeader = document.querySelector('.top-header');
    function setHeaderState() {
        if (!topHeader) return;
        if (window.scrollY > 10) topHeader.classList.add('scrolled');
        else topHeader.classList.remove('scrolled');
    }
    window.addEventListener('scroll', setHeaderState, { passive: true });
    setHeaderState();

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
            iconWrap.className = icon ? icon.className : 'fas fa-link';

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

    const counters = document.querySelectorAll('.stat-number');
    if (counters.length) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const counter = entry.target;
                const target = Number(counter.getAttribute('data-count') || 0);
                let count = 0;
                const update = setInterval(() => {
                    count += Math.ceil(target / 50);
                    if (count >= target) {
                        counter.innerText = String(target);
                        clearInterval(update);
                    } else {
                        counter.innerText = String(count);
                    }
                }, 20);
                counterObserver.unobserve(counter);
            });
        }, { threshold: 0.5 });
        counters.forEach(c => counterObserver.observe(c));
    }

    const skillLevels = document.querySelectorAll('.skill-level');
    if (skillLevels.length) {
        const skillObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const bar = entry.target;
                const level = bar.getAttribute('data-level');
                if (level) bar.style.width = level + '%';
                skillObserver.unobserve(bar);
            });
        }, { threshold: 0.3 });
        skillLevels.forEach(bar => skillObserver.observe(bar));
    }

    const thumbItems = document.querySelectorAll('.thumbnail-item');
    const refCards = document.querySelectorAll('.reference-card');
    if (thumbItems.length && refCards.length) {
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
    }

    const chatToggle = document.getElementById('chatbotToggle');
    const chatContainer = document.getElementById('chatbotContainer');
    const chatClose = document.getElementById('chatbotClose');
    const chatSend = document.getElementById('chatbotSend');
    const chatInput = document.getElementById('chatbotInput');
    const chatMessages = document.getElementById('chatbotMessages');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    function addMessage(text, sender) {
        if (!chatMessages) return;
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('message', sender);
        msgDiv.textContent = text;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function botResponse(question) {
        let answer = "I'm not sure. Try asking about experience, skills, or contact.";
        const q = String(question || '').toLowerCase();
        if (q.includes('experience') || q.includes('work')) answer = "Njabulo is currently IT Officer & CSPro Programmer at Central Statistical Office, previously at Ministry of Economic Planning developing Visitor Management System.";
        else if (q.includes('skill') || q.includes('technologies')) answer = "Core skills: CSPro/CSWeb, PHP, JavaScript, System Administration, Network Configuration, and Database Management.";
        else if (q.includes('contact') || q.includes('email') || q.includes('reach')) answer = "You can email njabulob.mavuso@gmail.com, WhatsApp +268 7979 2770, or connect on LinkedIn.";
        else if (q.includes('project')) answer = "Featured projects: PHC Dashboard, SmartSchoolHubsz, and Visitor Management System.";
        addMessage(answer, 'bot');
    }

    if (chatToggle && chatContainer) {
        chatToggle.addEventListener('click', () => {
            chatContainer.classList.toggle('active');
            if (chatContainer.classList.contains('active')) chatToggle.style.display = 'none';
            else chatToggle.style.display = 'flex';
        });
    }
    if (chatClose && chatToggle && chatContainer) {
        chatClose.addEventListener('click', () => {
            chatContainer.classList.remove('active');
            chatToggle.style.display = 'flex';
        });
    }

    if (chatSend && chatInput) {
        chatSend.addEventListener('click', () => {
            const msg = chatInput.value.trim();
            if (!msg) return;
            addMessage(msg, 'user');
            chatInput.value = '';
            setTimeout(() => botResponse(msg), 500);
        });
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') chatSend.click();
        });
    }

    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const question = btn.getAttribute('data-question') || '';
            addMessage(question, 'user');
            setTimeout(() => botResponse(question), 500);
        });
    });

    const yearSpan = document.getElementById('currentYear');
    if (yearSpan) yearSpan.textContent = String(new Date().getFullYear());

    const bounceCards = document.querySelectorAll('.bounce-card');
    if (bounceCards.length) {
        const bounceObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.2 });
        bounceCards.forEach(c => bounceObserver.observe(c));
    }

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(contactForm);
            const messageDiv = document.getElementById('formMessage');
            if (!messageDiv) return;

            try {
                const response = await fetch('contact-process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                messageDiv.className = 'form-message';
                if (result && result.success) {
                    messageDiv.classList.add('success');
                    messageDiv.textContent = result.message || 'Message sent successfully!';
                    contactForm.reset();
                } else {
                    messageDiv.classList.add('error');
                    messageDiv.textContent = (result && result.message) ? result.message : 'Error sending message. Please try again.';
                }
            } catch (error) {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'Network error. Please try again.';
            }
        });
    }
});
