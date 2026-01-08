<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Questions : {{ $quiz->title }}
        </h2>
    </x-slot>

    @include('components.quizzes-nav')

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Liste des questions ({{ $questions->count() }})
                            </h3>
                            <p class="text-sm text-gray-500">
                                Durée du quiz : {{ $quiz->duration }} minutes
                            </p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('quizzes.index') }}" 
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Retour aux quizzes
                            </a>
                            <a href="{{ route('questions.create', $quiz) }}" 
                               class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                + Ajouter une question
                            </a>
                        </div>
                    </div>

                    @if($questions->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune question</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Commencez par ajouter votre première question.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('questions.create', $quiz) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Ajouter une question
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($questions as $index => $question)
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 font-medium">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <h4 class="text-lg font-medium text-gray-900">
                                                        {{ $question->question_text }}
                                                    </h4>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            @if($question->type === 'multiple_choice') bg-blue-100 text-blue-800
                                                            @elseif($question->type === 'multiple_answer') bg-purple-100 text-purple-800
                                                            @elseif($question->type === 'text') bg-green-100 text-green-800
                                                            @else bg-yellow-100 text-yellow-800 @endif">
                                                            @switch($question->type)
                                                                @case('multiple_choice')
                                                                    QCM (1 réponse)
                                                                @break
                                                                @case('multiple_answer')
                                                                    Réponses multiples
                                                                @break
                                                                @case('text')
                                                                    Réponse texte
                                                                @break
                                                                @case('file')
                                                                    Fichier
                                                                @break
                                                            @endswitch
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $question->points }} point(s)
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($question->options && in_array($question->type, ['multiple_choice', 'multiple_answer']))
                                                <div class="mt-4 ml-11">
                                                    <p class="text-sm font-medium text-gray-700 mb-2">Options :</p>
                                                    <ul class="space-y-2">
                                                        @foreach($question->options as $optionIndex => $option)
                                                            <li class="flex items-center">
                                                                <span class="inline-block w-6 h-6 mr-2 text-center text-sm 
                                                                    @if(($question->type === 'multiple_choice' && $question->correct_answer == $optionIndex) ||
                                                                        ($question->type === 'multiple_answer' && in_array($optionIndex, $question->correct_answer)))
                                                                        bg-green-100 text-green-800 border border-green-300 rounded
                                                                    @else
                                                                        bg-gray-100 text-gray-800 border border-gray-300 rounded
                                                                    @endif">
                                                                    {{ chr(65 + $optionIndex) }}
                                                                </span>
                                                                <span class="text-gray-700">{{ $option }}</span>
                                                                @if(($question->type === 'multiple_choice' && $question->correct_answer == $optionIndex) ||
                                                                    ($question->type === 'multiple_answer' && in_array($optionIndex, $question->correct_answer)))
                                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                        ✓ Correct
                                                                    </span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            
                                            @if($question->type === 'text' && isset($question->correct_answer['text']))
                                                <div class="mt-4 ml-11">
                                                    <p class="text-sm font-medium text-gray-700 mb-1">Réponse modèle :</p>
                                                    <p class="text-gray-600 bg-gray-50 p-3 rounded border border-gray-200">
                                                        {{ $question->correct_answer['text'] }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <a href="{{ route('questions.edit', [$quiz, $question]) }}" 
                                               class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 text-sm">
                                                Modifier
                                            </a>
                                            <form action="{{ route('questions.destroy', [$quiz, $question]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Supprimer cette question ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-blue-800">
                                        Total des points : <span class="font-bold">{{ $questions->sum('points') }}</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-blue-800">
                                        Temps estimé par question : 
                                        <span class="font-bold">
                                            {{ round($quiz->duration / max($questions->count(), 1), 1) }} min
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>