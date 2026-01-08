@extends('layouts.app')

@section('title', 'Plateforme d\'évaluation en ligne')

@section('content')
<div class="relative overflow-hidden">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-primary-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300 text-sm font-medium mb-8">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Plateforme d'évaluation professionnelle
            </div>
            
            <!-- Titre principal -->
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Évaluez vos apprenants
                <span class="block text-primary-600 dark:text-primary-400">avec simplicité et efficacité</span>
            </h1>
            
            <!-- Sous-titre -->
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-10">
                Créez, gérez et corrigez des évaluations en ligne. Supportez tous les types de questions : QCM, texte, fichiers, et bien plus encore.
            </p>
            
            <!-- Boutons CTA -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                @auth
                    <a href="{{ route('quizzes.index') }}" class="btn btn-primary px-8 py-4 text-base font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Créer une évaluation
                    </a>
                    <a href="{{ route('participant.quizzes.index') }}" class="btn btn-outline px-8 py-4 text-base font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Passer un quiz
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary px-8 py-4 text-base font-medium">
                        Commencer gratuitement
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline px-8 py-4 text-base font-medium">
                        Se connecter
                    </a>
                @endauth
            </div>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-3xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">100%</div>
                    <div class="text-gray-600 dark:text-gray-400">En ligne</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">∞</div>
                    <div class="text-gray-600 dark:text-gray-400">Types de questions</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">24/7</div>
                    <div class="text-gray-600 dark:text-gray-400">Disponibilité</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">100%</div>
                    <div class="text-gray-600 dark:text-gray-400">Responsive</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Fonctionnalités puissantes
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Tout ce dont vous avez besoin pour créer et gérer des évaluations professionnelles
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card card-hover p-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Multiples types de questions</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        QCM, réponses multiples, texte libre, fichiers... Adaptez vos évaluations à tous les besoins pédagogiques.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="card card-hover p-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Timer intelligent</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Décompte en temps réel avec sauvegarde automatique et soumission à la fin du temps.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="card card-hover p-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Gestion simplifiée</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Interface intuitive pour créer, modifier et organiser vos évaluations en quelques clics.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="card card-hover p-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Correction automatique</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Gain de temps avec la correction automatique pour les QCM et réponses multiples.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="card card-hover p-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Statistiques détaillées</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Analysez les performances de vos apprenants avec des rapports complets et visuels.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="card card-hover p-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">API & Intégrations</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Connectez EvalPro à vos outils existants grâce à notre API robuste et documentée.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-20 bg-gradient-to-r from-primary-500 to-primary-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Prêt à révolutionner vos évaluations ?
            </h2>
            <p class="text-xl text-primary-100 mb-10 max-w-2xl mx-auto">
                Rejoignez des centaines d'éducateurs qui utilisent déjà EvalPro pour simplifier leur processus d'évaluation.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('quizzes.create') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium rounded-lg text-primary-600 bg-white hover:bg-gray-50 shadow-lg hover:shadow-xl transition-all duration-300">
                        Créer ma première évaluation
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium rounded-lg text-primary-600 bg-white hover:bg-gray-50 shadow-lg hover:shadow-xl transition-all duration-300">
                        Commencer gratuitement
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium rounded-lg text-white border-2 border-white hover:bg-white/10 shadow-lg hover:shadow-xl transition-all duration-300">
                        Déjà un compte ? Se connecter
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection