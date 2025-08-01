<?php
require_once __DIR__ . '/../php/config.php';

try {
    $conn = getDBConnection();
    $stmt = $conn->query('SELECT * FROM cursos');
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro ao carregar cursos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colégio Estadual Professor Nilo Brandão</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header id="header">
        <div class="container header-container">
            <a href="../base/base.php" class="logo">
                <img src="https://via.placeholder.com/50" alt="Logo Colégio Nilo Brandão" class="logo-img">
                <div class="logo-text">
                    <h1>Colégio Nilo Brandão</h1>
                    <p>Educação pública de qualidade</p>
                </div>
            </a>
            
            <div class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav>
    <ul class="nav-menu" id="navMenu">
        <li class="nav-item">
            <a href="../base/base.php" class="nav-link">Início</a>
        </li>
        <li class="nav-item">
            <a href="../php/quem-somos.php" class="nav-link">Quem Somos</a>
            <ul class="submenu">
                <li class="submenu-item"><a href="../php/nossa-historia.php" class="submenu-link">Nossa História</a></li>
                <li class="submenu-item"><a href="../php/proposta-pedagogica.php" class="submenu-link">Proposta Pedagógica</a></li>
                <li class="submenu-item"><a href="../php/direcao-equipe.php" class="submenu-link">Direção e Equipe</a></li>
                <li class="submenu-item"><a href="../php/estrutura-fisica.php" class="submenu-link">Estrutura Física</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">Ensino</a>
            <ul class="submenu">
    <li><a href="curso.php?slug=ensino-fundamental-ii">Fundamental II</a></li>
    <li><a href="curso.php?slug=ensino-medio">Ensino Médio</a></li>
    <li><a href="curso.php?slug=ensino-tecnico">Curso Técnico</a></li>
</ul>
        </li>
        <li class="nav-item">
            <a href="../php/noticias.php" class="nav-link">Notícias</a>
        </li>
        <li class="nav-item">
            <a href="../php/contato.php" class="nav-link">Contato</a>
        </li>
    </ul>
</nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-content">
            <h1>Excelência em Educação Pública</h1>
            <p>Há mais de 50 anos formando cidadãos críticos e preparados para os desafios do futuro em Curitiba</p>
            <div class="hero-buttons">
                <a href="#" class="btn">Conheça nossa escola</a>
                <a href="#" class="btn btn-outline">Processo seletivo</a>
            </div>
        </div>
    </section>

    <!-- Sobre -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-container">
                <div class="about-content">
                    <h2 class="section-title">Sobre Nós</h2>
                    <p>O Colégio Estadual Professor Nilo Brandão é uma instituição pública de ensino reconhecida por sua excelência acadêmica e compromisso com a formação integral dos estudantes.</p>
                    <p>Localizado no coração de Curitiba, nosso colégio oferece uma estrutura moderna e professores altamente qualificados para atender alunos desde a Educação Infantil até o Ensino Médio e Técnico.</p>
                    
                    <div class="about-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h3>Educação de Qualidade</h3>
                                <p>Métodos pedagógicos inovadores e resultados comprovados.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h3>Formação Integral</h3>
                                <p>Desenvolvimento intelectual, físico, emocional e social.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="about-img">
                    <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1472&q=80" alt="Estudantes no colégio">
                </div>
            </div>
        </div>
    </section>

    <!-- Cursos Section -->
    <section class="courses" id="courses">
        <div class="container">
            <div class="courses-header">
                <h2 class="section-title">Nossos Cursos</h2>
                <p>Oferecemos uma variedade de modalidades de ensino para atender diferentes necessidades e faixas etárias.</p>
            </div>
            
            <div class="courses-grid">
    <?php foreach($cursos as $curso): ?>
        <div class="course-card">
            <div class="course-img">
                <?php
                // Caminho correto para as imagens
                $imagem_path = '../assets/images/cursos/' . htmlspecialchars($curso['imagem'] ?? '');
                if (!empty($curso['imagem']) && file_exists($imagem_path)): ?>
                    <img src="<?= $imagem_path ?>" alt="<?= htmlspecialchars($curso['nome']) ?>">
                <?php else: ?>
                    <img src="../assets/images/cursos/default.jpg" alt="Imagem padrão">
                <?php endif; ?>
            </div>
            
            <div class="course-content">
                <h3><?= htmlspecialchars($curso['nome']) ?></h3>
                
                <!-- Faixa etária formatada -->
                <div class="course-meta">
                    <?php if (!empty($curso['idade'])): ?>
                        <span><i class="fas fa-user-graduate"></i> <?= htmlspecialchars($curso['idade']) ?></span>
                    <?php elseif (!empty($curso['idade_min']) && !empty($curso['idade_max'])): ?>
                        <span><i class="fas fa-user-graduate"></i> <?= $curso['idade_min'] ?>-<?= $curso['idade_max'] ?> anos</span>
                    <?php endif; ?>
                    
                    <span><i class="far fa-clock"></i> <?= htmlspecialchars($curso['periodo']) ?></span>
                    <span><i class="fas fa-users"></i> <?= htmlspecialchars($curso['vagas']) ?> vagas</span>
                </div>
                
                <p><?= htmlspecialchars($curso['descricao']) ?></p>
                <a href="curso.php?slug=<?= htmlspecialchars($curso['slug']) ?>" class="btn">Saiba Mais</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
    </section>

    <!-- Números -->
    <section class="stats" id="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Anos de história</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1.200</div>
                    <div class="stat-label">Alunos matriculados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">65</div>
                    <div class="stat-label">Profissionais dedicados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Índice de aprovação</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="testimonials-header">
                <h2 class="section-title">Depoimentos</h2>
                <p>O que alunos, ex-alunos e pais dizem sobre nossa escola</p>
            </div>
            
            <div class="testimonial-slider">
                <div class="testimonial-item active">
                    <p class="testimonial-text">"Estudar no Nilo Brandão foi fundamental para minha formação. Os professores são dedicados e a estrutura da escola permite desenvolver diversas habilidades além do conteúdo acadêmico."</p>
                    <div class="testimonial-author">Maria Silva</div>
                    <div class="testimonial-role">Ex-aluna, hoje estudante de Medicina</div>
                </div>
                
                <div class="testimonial-item">
                    <p class="testimonial-text">"Como mãe, fico tranquila sabendo que meu filho está em uma escola que valoriza não apenas as notas, mas o desenvolvimento humano e social dos estudantes."</p>
                    <div class="testimonial-author">Ana Oliveira</div>
                    <div class="testimonial-role">Mãe de aluno do 5º ano</div>
                </div>
                
                <div class="testimonial-item">
                    <p class="testimonial-text">"O ensino técnico integrado ao médio me deu uma grande vantagem no mercado de trabalho. Hoje, já estou empregado na área que escolhi ainda durante o ensino médio."</p>
                    <div class="testimonial-author">Carlos Mendes</div>
                    <div class="testimonial-role">Ex-aluno do curso técnico</div>
                </div>
                
                <div class="testimonial-nav">
                    <div class="testimonial-dot active" data-slide="0"></div>
                    <div class="testimonial-dot" data-slide="1"></div>
                    <div class="testimonial-dot" data-slide="2"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Colégio Nilo Brandão</h3>
                    <p>Educação pública de qualidade em Curitiba há mais de 50 anos, formando cidadãos críticos e preparados para os desafios do futuro.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Links Rápidos</h3>
                    <ul>
                        <li><a href="#" class="footer-link">Início</a></li>
                        <li><a href="#" class="footer-link">Sobre Nós</a></li>
                        <li><a href="#" class="footer-link">Cursos</a></li>
                        <li><a href="#" class="footer-link">Notícias</a></li>
                        <li><a href="#" class="footer-link">Contato</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Cursos</h3>
                    <ul>
                    
                        <li><a href="#" class="footer-link">Ensino Fundamental II</a></li>
                        <li><a href="#" class="footer-link">Ensino Médio</a></li>
                        <li><a href="#" class="footer-link">Cursos Técnicos</a></li>
                        <li><a href="#" class="footer-link">Atividades Extracurriculares</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contato</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Rua Exemplo, 123 - Curitiba/PR</li>
                        <li><i class="fas fa-phone"></i> (41) 1234-5678</li>
                        <li><i class="fas fa-envelope"></i> contato@nilo-brandao.edu.br</li>
                        <li><i class="fas fa-clock"></i> Seg-Sex: 7h30 às 18h</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Colégio Estadual Professor Nilo Brandão. Todos os direitos reservados.</p>
                <p><a href="#">Política de Privacidade</a> | <a href="#">Termos de Uso</a></p>
            </div>
        </div>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>