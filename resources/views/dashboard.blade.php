@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('header', 'Tableau de bord')

@section('content')
<div class="container py-5">
    <!-- Statistiques -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h3 class="h2 fw-bold mb-2">12</h3>
                    <p class="text-muted mb-0">Évaluations</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="h2 fw-bold mb-2">156</h3>
                    <p class="text-muted mb-0">Participants</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h3 class="h2 fw-bold mb-2">78%</h3>
                    <p class="text-muted mb-0">Taux de réussite</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="h2 fw-bold mb-2">15min</h3>
                    <p class="text-muted mb-0">Temps moyen</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="h4 fw-bold mb-4">Bienvenue, {{ Auth::user()->name }}!</h3>
                    <p class="text-muted mb-4">
                        Vous êtes connecté à votre tableau de bord EvalPro. Voici un aperçu de vos activités récentes.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Évaluations actives</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="py-2 border-bottom">
                                            <strong>Quiz JavaScript</strong><br>
                                            <small class="text-muted">15 participants • 85% réussite</small>
                                        </li>
                                        <li class="py-2">
                                            <strong>Test PHP Basics</strong><br>
                                            <small class="text-muted">8 participants • 92% réussite</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Derniers résultats</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="py-2 border-bottom">
                                            <strong>Marie Dubois</strong><br>
                                            <small class="text-success">95% - Excellent</small>
                                        </li>
                                        <li class="py-2">
                                            <strong>Jean Martin</strong><br>
                                            <small class="text-warning">68% - Satisfaisant</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="h5 fw-bold mb-3">Actions rapides</h4>
                    <div class="d-grid gap-2">
                        <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Créer une évaluation
                        </a>
                        <a href="{{ route('quizzes.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-journal-text me-2"></i>
                            Mes évaluations
                        </a>
                        <a href="{{ route('participant.quizzes.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-clock me-2"></i>
                            Passer un quiz
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="h6 fw-bold mb-3">Profil</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary-gradient d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px; color: white; font-size: 1.25rem; font-weight: 500;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="ms-3">
                            <strong>{{ Auth::user()->name }}</strong>
                            <div class="text-muted small">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-gear me-2"></i>
                        Modifier le profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection