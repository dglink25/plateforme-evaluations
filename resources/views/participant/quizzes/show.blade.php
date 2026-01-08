<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- En-tête du quiz -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $quiz->title }}</h1>
                        
                        @if($quiz->description)
                            <div class="prose max-w-none mb-6">
                                <p class="text-gray-700">{{ $quiz->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Informations du quiz -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-600">Durée</p>
                                    <p class="text-2xl font-bold text-blue-700">{{ $quiz->duration }} min</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <div>
                                    <p class="text-sm text-purple-600">Questions</p>
                                    <p class="text-2xl font-bold text-purple-700">{{ $quiz->questions->count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm text-green-600">Statut</p>
                                    <p class="text-2xl font-bold text-green-700">
                                        @if($participation)
                                            @if($participation->status === 'completed' || $participation->status === 'graded')
                                                Terminé
                                            @else
                                                En cours
                                            @endif
                                        @else
                                            Disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-yellow-800">Instructions importantes</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Le temps commence dès que vous cliquez sur "Commencer le Quiz"</li>
                                        <li>Vous ne pourrez pas mettre en pause le chronomètre</li>
                                        <li>Assurez-vous d'avoir une connexion Internet stable</li>
                                        <li>Les réponses sont enregistrées automatiquement</li>
                                        <li>Le quiz se soumet automatiquement à la fin du temps</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton d'action -->
                    <div class="flex justify-center">
                        @if($participation)
                            @if($participation->status === 'in_progress')
                                <a href="{{ route('participant.quizzes.take', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                                   class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300">
                                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Reprendre le Quiz
                                </a>
                            @else
                                <a href="{{ route('participant.quizzes.result', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                                   class="inline-flex items-center px-8 py-4 bg-green-600 text-white text-lg font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300">
                                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Voir mes résultats
                                </a>
                            @endif
                        @else
                            <form action="{{ route('participant.quizzes.start', $quiz) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all transform hover:scale-105"
                                        onclick="return confirm('Êtes-vous prêt à commencer ? Le chronomètre démarrera immédiatement.')">
                                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Commencer le Quiz
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Retour à la liste -->
                    <div class="mt-8 text-center">
                        <a href="{{ route('participant.quizzes.index') }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            ← Retour à la liste des quizzes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>