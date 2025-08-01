<?php
require_once 'config.php';

// --- PARTE 1: PROCESSAMENTO DO FORMULÁRIO DE ADMIN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'slug' => criarSlug($_POST['nome']),
        'descricao' => $_POST['descricao'],
        'periodo' => $_POST['periodo'],
        'vagas' => $_POST['vagas'],
        'idade_min' => $_POST['idade_min'],
        'idade_max' => $_POST['idade_max'],
        'imagem' => processarUploadImagem()
    ];

    if (!empty($_POST['id'])) {
        atualizarCurso($conn, $dados, $_POST['id']);
    } else {
        inserirCurso($conn, $dados);
    }
}

// --- PARTE 2: EXIBIÇÃO DO CURSO ---
$slug = $_GET['slug'] ?? '';
$curso = buscarCurso($conn, $slug);

if (!$curso) {
    header("Location: 404.php");
    exit;
}

// Debug: Verificar dados do curso
error_log("Dados do curso: " . print_r($curso, true));

$conteudoEspecifico = carregarConteudoEspecifico($slug);
$pageTitle = htmlspecialchars($curso['nome']) . " - Colégio Nilo Brandão";

include 'header.php';
?>

<!-- SEÇÃO PRINCIPAL DO CURSO -->
<section class="course-detail">
    <div class="container">
        <div class="course-detail-container">
            <!-- Seção da Imagem do Curso -->
            <div class="course-image">
                <?php if (!empty($curso['imagem']) && file_exists("../assets/images/cursos/" . $curso['imagem'])): ?>
                    <img src="../assets/images/cursos/<?= htmlspecialchars($curso['imagem']) ?>" 
                         alt="<?= htmlspecialchars($curso['nome']) ?>">
                <?php else: ?>
                    <img src="../assets/images/cursos/default.jpg" alt="Imagem padrão do curso">
                <?php endif; ?>
            </div>
            
            <div class="course-detail-content">
                <h1><?= htmlspecialchars($curso['nome']) ?></h1>
                
                <!-- Metadados do Curso -->
                <div class="course-meta">
    <?php if (!empty($curso['idade'])): ?>
        <!-- Mostra direto o campo idade se existir -->
        <div class="meta-item">
            <i class="fas fa-user-graduate"></i>
            <span><?= htmlspecialchars($curso['idade']) ?></span>
        </div>
    <?php elseif (!empty($curso['idade_min']) || !empty($curso['idade_max'])): ?>
        <!-- Mostra faixa etária se tiver idade_min ou idade_max -->
        <div class="meta-item">
            <i class="fas fa-user-graduate"></i>
            <span>
                <?= !empty($curso['idade_min']) ? $curso['idade_min'] : '0' ?>
                <?= (!empty($curso['idade_min']) && !empty($curso['idade_max'])) ? '-' : '' ?>
                <?= !empty($curso['idade_max']) ? $curso['idade_max'] : '+' ?> anos
            </span>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($curso['periodo'])): ?>
        <div class="meta-item">
            <i class="far fa-clock"></i>
            <span><?= htmlspecialchars($curso['periodo']) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($curso['vagas'])): ?>
        <div class="meta-item">
            <i class="fas fa-users"></i>
            <span><?= htmlspecialchars($curso['vagas']) ?> vagas</span>
        </div>
    <?php endif; ?>
</div>
                
                <!-- Descrição do Curso -->
                <div class="course-description">
                    <h2>Sobre o Curso</h2>
                    <?php if (!empty($curso['descricao'])): ?>
                        <p><?= nl2br(htmlspecialchars($curso['descricao'])) ?></p>
                    <?php else: ?>
                        <p>Descrição não disponível.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Destaques do Curso -->
                <?php if (!empty($conteudoEspecifico['destaques'])): ?>
                    <div class="course-highlights">
                        <h2>Destaques</h2>
                        <ul>
                            <?php foreach ($conteudoEspecifico['destaques'] as $destaque): ?>
                                <li><?= htmlspecialchars($destaque) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if ($slug === 'ensino-fundamental-ii' || $slug === 'ensino-medio'): ?>
    <a href="https://www.areadoaluno.seed.pr.gov.br/servicosaoaluno/pages/publico/login.jsf;jsessionid=WQ0ZDD_gnkMxi23yiS-fLucrgkgyMXmBj0etg2Ox.sseed75003?dswid=2039" class="btn" target="_blank">Solicitar Matrícula</a>
<?php else: ?>
    <a href="matricula.php?curso=<?= $slug ?>" class="btn">Solicitar Matrícula</a>
<?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Galeria de Imagens -->
<?php if (!empty($conteudoEspecifico['imagens'])): ?>
<section class="course-gallery">
    <div class="container">
        <h2>Galeria</h2>
        <div class="gallery-grid">
            <?php foreach ($conteudoEspecifico['imagens'] as $imagem): ?>
                <?php if (file_exists("../assets/images/cursos/" . $imagem)): ?>
                    <div class="gallery-item">
                        <img src="../assets/images/cursos/<?= htmlspecialchars($imagem) ?>" 
                             alt="<?= htmlspecialchars($curso['nome']) ?>">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>

<?php
// --- FUNÇÕES AUXILIARES ---
function criarSlug($nome) {
    return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nome));
}

function processarUploadImagem() {
    if (empty($_FILES['imagem']['name'])) return null;
    
    $pasta = '../assets/images/cursos/';
    if (!file_exists($pasta)) {
        mkdir($pasta, 0777, true);
    }
    
    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid() . '.' . $extensao;
    
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $nomeArquivo)) {
        return $nomeArquivo;
    }
    return null;
}

function buscarCurso($conn, $slug) {
    try {
        $stmt = $conn->prepare("SELECT * FROM cursos WHERE slug = ?");
        $stmt->execute([$slug]);
        $curso = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Verificar dados retornados do banco
        error_log("Curso encontrado: " . print_r($curso, true));
        
        return $curso;
    } catch (PDOException $e) {
        error_log("Erro ao buscar curso: " . $e->getMessage());
        return false;
    }
}


function carregarConteudoEspecifico($slug) {
    $conteudos = [
        'ensino-fundamental-ii' => [
            'destaques' => ['Aulas de reforço', 'Projetos interdisciplinares'],
            'imagens' => ['fundamental1.jpg', 'fundamental2.jpg']
        ],
        'ensino-medio' => [
            'destaques' => ['Preparação ENEM', 'Simulados periódicos'],
            'imagens' => ['medio1.jpg', 'medio2.jpg']
        ],
        'ensino-tecnico' => [
            'destaques' => ['Laboratórios modernos', 'Certificação profissional'],
            'imagens' => ['tecnico1.jpg', 'tecnico2.jpg'],
            'modulos' => ['Programação Web', 'Banco de Dados']
        ]
    ];
    
    return $conteudos[$slug] ?? null;
}

function inserirCurso($conn, $dados) {
    $sql = "INSERT INTO cursos (nome, slug, descricao, periodo, vagas, idade_min, idade_max, imagem) 
            VALUES (:nome, :slug, :descricao, :periodo, :vagas, :idade_min, :idade_max, :imagem)";
    $conn->prepare($sql)->execute($dados);
}

function atualizarCurso($conn, $dados, $id) {
    $sql = "UPDATE cursos SET 
            nome = :nome, slug = :slug, descricao = :descricao, 
            periodo = :periodo, vagas = :vagas, 
            idade_min = :idade_min, idade_max = :idade_max";
    
    if (!empty($dados['imagem'])) {
        $sql .= ", imagem = :imagem";
    }
    
    $sql .= " WHERE id = :id";
    $dados['id'] = $id;
    $conn->prepare($sql)->execute($dados);
}
?>