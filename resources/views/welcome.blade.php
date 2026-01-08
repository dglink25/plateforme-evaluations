@extends('layouts.app')

@section('title', 'Plateforme d\'évaluation en ligne')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-10 mx-auto text-center">
                <!-- Badge -->
                <div class="hero-badge">
                    <i class="bi bi-lightning-fill"></i>
                    Plateforme d'évaluation professionnelle
                </div>
                
                <!-- Titre principal -->
                <h1 class="display-3 fw-bold mb-4">
                    Évaluez vos apprenants
                    <span class="text-gradient d-block">avec simplicité et efficacité</span>
                </h1>
                
                <!-- Sous-titre -->
                <p class="lead text-muted mb-5">
                    Créez, gérez et corrigez des évaluations en ligne. Supportez tous les types de questions : QCM, texte, fichiers, et bien plus encore.
                </p>
                
                <!-- Boutons CTA -->
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-center mb-5">
                    @auth
                        <a href="{{ route('quizzes.index') }}" class="btn btn-primary btn-lg px-5 py-3">
                            <i class="bi bi-plus-circle me-2"></i>
                            Créer une évaluation
                        </a>
                        <a href="{{ route('participant.quizzes.index') }}" class="btn btn-outline-primary btn-lg px-5 py-3">
                            <i class="bi bi-clock me-2"></i>
                            Passer un quiz
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 py-3">
                            Commencer gratuitement
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5 py-3">
                            Se connecter
                        </a>
                    @endauth
                </div>
                
                <!-- Statistiques -->
                <div class="row justify-content-center mt-5 pt-5">
                    <div class="col-6 col-md-3 mb-4">
                        <div class="stat-number">100%</div>
                        <div class="text-muted">En ligne</div>
                    </div>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="stat-number">∞</div>
                        <div class="text-muted">Types de questions</div>
                    </div>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="stat-number">24/7</div>
                        <div class="text-muted">Disponibilité</div>
                    </div>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="stat-number">100%</div>
                        <div class="text-muted">Responsive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-4">Fonctionnalités puissantes</h2>
                <p class="lead text-muted">
                    Tout ce dont vous avez besoin pour créer et gérer des évaluations professionnelles
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="h4 fw-bold text-center mb-3">Multiples types de questions</h3>
                        <p class="text-muted text-center">
                            QCM, réponses multiples, texte libre, fichiers... Adaptez vos évaluations à tous les besoins pédagogiques.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h3 class="h4 fw-bold text-center mb-3">Timer intelligent</h3>
                        <p class="text-muted text-center">
                            Décompte en temps réel avec sauvegarde automatique et soumission à la fin du temps.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-layers"></i>
                        </div>
                        <h3 class="h4 fw-bold text-center mb-3">Gestion simplifiée</h3>
                        <p class="text-muted text-center">
                            Interface intuitive pour créer, modifier et organiser vos évaluations en quelques clics.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-check-all"></i>
                        </div>
                        <h3 class="h4 fw-bold text-center mb-3">Correction automatique</h3>
                        <p class="text-muted text-center">
                            Gain de temps avec la correction automatique pour les QCM et réponses multiples.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h3 class="h4 fw-bold text-center mb-3">Statistiques détaillées</h3>
                        <p class="text-muted text-center">
                            Analysez les performances de vos apprenants avec des rapports complets et visuels.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-plug"></i>
                        </div>
                        <h3 class="h4 fw-bold text-center mb-3">API & Intégrations</h3>
                        <p class="text-muted text-center">
                            Connectez EvalPro à vos outils existants grâce à notre API robuste et documentée.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary-gradient text-white">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-4">Prêt à révolutionner vos évaluations ?</h2>
                <p class="lead mb-5 opacity-75">
                    Rejoignez des centaines d'éducateurs qui utilisent déjà EvalPro pour simplifier leur processus d'évaluation.
                </p>
                
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                    @auth
                        <a href="{{ route('quizzes.create') }}" class="btn btn-light btn-lg px-5 py-3">
                            Créer ma première évaluation
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3">
                            Commencer gratuitement
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3">
                            Déjà un compte ? Se connecter
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection