
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="card-title text-warning mb-0">
            <i class="fas fa-sitemap me-2"></i>Tournament Bracket: <?= esc($tournament['name']) ?>
        </h5>
        <div>
            <a href="<?= base_url('admin/tournaments/manage/' . $tournament['id']) ?>" class="btn btn-outline-warning me-2">
                <i class="fas fa-cog me-2"></i>Manage Matches
            </a>
            <a href="<?= base_url('admin/tournaments') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Tournaments
            </a>
        </div>
    </div>
</div>

<div class="tournament-bracket-container">
    <?php if (!empty($rounds)): ?>
        <div class="bracket-wrapper">
            <?php 
            $roundNames = [1 => 'Round 1', 2 => 'Quarter Finals', 3 => 'Semi Finals', 4 => 'Final'];
            foreach ($rounds as $roundNumber => $roundMatches): 
            ?>
                <div class="bracket-round" data-round="<?= $roundNumber ?>">
                    <div class="round-header">
                        <h6 class="text-warning text-center mb-3">
                            <?= $roundNames[$roundNumber] ?? "Round $roundNumber" ?>
                        </h6>
                    </div>
                    <div class="matches-container">
                        <?php foreach ($roundMatches as $match): ?>
                            <div class="match-bracket <?= $match['status'] === 'completed' ? 'completed' : 'pending' ?>">
                                <div class="team-slot <?= $match['winner_team_id'] == $match['team1_id'] ? 'winner' : '' ?>">
                                    <span class="team-name"><?= esc($match['team1_name']) ?></span>
                                    <?php if (!empty($match['team1_score'])): ?>
                                        <span class="score"><?= $match['team1_score'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($match['winner_team_id'] == $match['team1_id']): ?>
                                        <i class="fas fa-crown text-warning ms-1"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="team-slot <?= $match['winner_team_id'] == $match['team2_id'] ? 'winner' : '' ?>">
                                    <span class="team-name"><?= esc($match['team2_name']) ?></span>
                                    <?php if (!empty($match['team2_score'])): ?>
                                        <span class="score"><?= $match['team2_score'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($match['winner_team_id'] == $match['team2_id']): ?>
                                        <i class="fas fa-crown text-warning ms-1"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="match-connector"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($tournament['status'] === 'completed' && !empty($tournament['winner_team_id'])): ?>
            <div class="winner-section text-center mt-5">
                <div class="card bg-dark border-warning">
                    <div class="card-body">
                        <h4 class="text-warning mb-3">
                            <i class="fas fa-trophy me-2"></i>Tournament Champion
                        </h4>
                        <h2 class="text-light mb-0">
                            <i class="fas fa-crown text-warning me-2"></i>
                            Winner Team
                        </h2>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="card bg-dark border-secondary">
            <div class="card-body text-center py-5">
                <i class="fas fa-sitemap fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No bracket available</h5>
                <p class="text-muted">Tournament matches need to be generated first.</p>
                <a href="<?= base_url('admin/tournaments/manage/' . $tournament['id']) ?>" class="btn btn-warning">
                    <i class="fas fa-cog me-2"></i>Manage Tournament
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.tournament-bracket-container {
    overflow-x: auto;
    padding: 20px 0;
}

.bracket-wrapper {
    display: flex;
    gap: 40px;
    min-width: 1200px;
    justify-content: center;
    align-items: flex-start;
}

.bracket-round {
    flex: 1;
    min-width: 250px;
}

.round-header {
    margin-bottom: 20px;
}

.matches-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
    align-items: center;
}

.match-bracket {
    position: relative;
    background: #2d2d2d;
    border: 2px solid #495057;
    border-radius: 8px;
    width: 220px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.match-bracket.completed {
    border-color: #28a745;
}

.match-bracket.pending {
    border-color: #ffc107;
}

.team-slot {
    padding: 12px 15px;
    border-bottom: 1px solid #495057;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #343a40;
    transition: all 0.3s ease;
}

.team-slot:last-child {
    border-bottom: none;
}

.team-slot.winner {
    background: #198754;
    color: white;
    font-weight: bold;
}

.team-name {
    font-size: 0.9rem;
    flex: 1;
}

.score {
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    margin-left: 10px;
}

.match-connector {
    position: absolute;
    right: -20px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 2px;
    background: #6c757d;
}

.bracket-round:last-child .match-connector {
    display: none;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .bracket-wrapper {
        min-width: 800px;
        gap: 20px;
    }
    
    .match-bracket {
        width: 180px;
    }
}

@media (max-width: 768px) {
    .bracket-wrapper {
        flex-direction: column;
        gap: 30px;
        min-width: auto;
        align-items: center;
    }
    
    .match-connector {
        display: none;
    }
    
    .bracket-round {
        min-width: auto;
        width: 100%;
        max-width: 300px;
    }
}

.winner-section .card {
    max-width: 500px;
    margin: 0 auto;
}

/* Animation for completed matches */
.match-bracket.completed {
    animation: completePulse 2s ease-in-out;
}

@keyframes completePulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}
</style>

<?= $this->endSection(); ?>
