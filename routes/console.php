<?php
use Illuminate\Support\Facades\Artisan;
Artisan::command('check-budget', function () {
    $trip = \App\Models\TripRequest::find(8);
    $this->info("Budget: " . $trip->budget);
    $this->info("Budget ID: " . $trip->budget_id);
    
    $budget = \App\Models\Budget::find($trip->budget_id);
    if ($budget) {
        $this->info("Budget Model: " . $budget->name . " / " . $budget->min_amount . " - " . $budget->max_amount);
    }
});
