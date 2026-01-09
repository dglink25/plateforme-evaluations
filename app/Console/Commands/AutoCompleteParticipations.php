<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Participation;
use Carbon\Carbon;

class AutoCompleteParticipations extends Command
{
    protected $signature = 'participations:autocomplete';
    protected $description = 'Vérifie et termine automatiquement les participations dont le temps est écoulé';

    public function handle()
    {
        $this->info('Recherche des participations à terminer automatiquement...');
        
        $participations = Participation::where('status', 'in_progress')
            ->with('quiz')
            ->get();
        
        $completedCount = 0;
        
        foreach ($participations as $participation) {
            if ($participation->shouldAutoComplete()) {
                $this->info("Participation #{$participation->id} - Temps écoulé, terminaison automatique...");
                
                // Terminer la participation
                $participation->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                
                // Calculer le score automatiquement
                $participation->calculateAndSaveAutoScore();
                
                $completedCount++;
                
                $this->line("✓ Participation #{$participation->id} terminée automatiquement");
            }
        }
        
        $this->newLine();
        $this->info("Terminé! {$completedCount} participation(s) terminée(s) automatiquement.");
        
        return Command::SUCCESS;
    }
}