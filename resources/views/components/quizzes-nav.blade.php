<nav class="bg-white shadow-sm mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex space-x-8">
                <x-nav-link :href="route('quizzes.index')" :active="request()->routeIs('quizzes.index')">
                    Mes Quizzes
                </x-nav-link>
                <x-nav-link :href="route('quizzes.create')" :active="request()->routeIs('quizzes.create')">
                    Créer un Quiz
                </x-nav-link>
            </div>
        </div>
    </div>
</nav>