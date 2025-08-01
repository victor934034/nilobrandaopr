// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.getElementById('header');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Mobile menu toggle
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.getElementById('navMenu');

menuToggle.addEventListener('click', function() {
    navMenu.classList.toggle('active');
    menuToggle.innerHTML = navMenu.classList.contains('active') ? 
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
});

// Testimonial slider
const testimonialItems = document.querySelectorAll('.testimonial-item');
const testimonialDots = document.querySelectorAll('.testimonial-dot');
let currentSlide = 0;

function showSlide(index) {
    testimonialItems.forEach(item => item.classList.remove('active'));
    testimonialDots.forEach(dot => dot.classList.remove('active'));
    
    testimonialItems[index].classList.add('active');
    testimonialDots[index].classList.add('active');
    currentSlide = index;
}

testimonialDots.forEach((dot, index) => {
    dot.addEventListener('click', () => showSlide(index));
});

// Auto slide change
setInterval(() => {
    let nextSlide = (currentSlide + 1) % testimonialItems.length;
    showSlide(nextSlide);
}, 5000);

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        }
    });
});

// Carregar cursos do banco de dados
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_cursos.php')
        .then(response => response.json())
        .then(cursos => {
            const container = document.getElementById('courses-container');
            
            cursos.forEach(curso => {
                const courseCard = document.createElement('div');
                courseCard.className = 'course-card';
                courseCard.innerHTML = `
                    <div class="course-img">
                        <img src="${curso.imagem}" alt="${curso.nome}">
                    </div>
                    <div class="course-content">
                        <span class="course-badge">${curso.idade}</span>
                        <h3 class="course-title">${curso.nome}</h3>
                        <p class="course-desc">${curso.descricao}</p>
                        <div class="course-meta">
                            <span><i class="far fa-clock"></i> ${curso.periodo}</span>
                            <span><i class="fas fa-user-graduate"></i> ${curso.vagas} vagas</span>
                        </div>
                    </div>
                `;
                container.appendChild(courseCard);
            });
        })
        .catch(error => console.error('Erro ao carregar cursos:', error));
});