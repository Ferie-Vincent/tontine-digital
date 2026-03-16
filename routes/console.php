<?php

use Illuminate\Support\Facades\Schedule;

// Sprint 1 — Automatisations du cycle de vie
Schedule::command('tontine:update-statuses')->dailyAt('06:00');
Schedule::command('tontine:auto-generate-tours')->dailyAt('06:15');
Schedule::command('tontine:auto-start-tours')->dailyAt('06:30');

// Commandes existantes
Schedule::command('tontine:send-reminders')->dailyAt('08:00');
Schedule::command('tontine:mark-late')->dailyAt('09:00');
Schedule::command('tontine:fail-expired')->dailyAt('10:00');

// Sprint 2 — Automatisations avancées
Schedule::command('tontine:check-payments')->everyTwoHours();
Schedule::command('tontine:collection-alerts')->dailyAt('10:30');
Schedule::command('tontine:auto-disburse')->dailyAt('11:00');
Schedule::command('tontine:generate-reports')->weeklyOn(1, '07:00');

// Sprint 3 — Optimisations
Schedule::command('tontine:archive-completed')->dailyAt('00:00');
Schedule::command('tontine:auto-reinstate')->dailyAt('07:00');

// Sprint 5 — Expérience utilisateur
Schedule::command('tontine:auto-close-tours')->dailyAt('07:30');
Schedule::command('tontine:send-digest')->dailyAt('08:30');
Schedule::command('tontine:remind-receipt-confirmation')->dailyAt('09:30');
