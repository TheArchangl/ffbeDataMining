<?php
    /**
     * User: aEnigma
     * Date: 09.02.2017
     * Time: 12:00
     */

    require_once dirname(__DIR__) . '/bootstrap.php';

    // "update" client files & read strings
    require_once __DIR__ . '/client_update.php';

    // mogking
    require_once __DIR__ . '/event.php';

    // units and beasts
    require_once __DIR__ . '/units.php';
    require_once __DIR__ . '/summons.php';

    // skills
    require_once __DIR__ . '/skills.php';
    require_once __DIR__ . '/enhancements.php';
    require_once __DIR__ . '/monster_skills.php'; // monster skills + skillsets
    require_once __DIR__ . '/unit_latent_skills.php';

    // items
    require_once __DIR__ . '/items.php';
    require_once __DIR__ . '/recipes.php';
    require_once __DIR__ . '/vision_cards.php';

    // world -> missions
    require_once __DIR__ . '/worlds.php';
    require_once __DIR__ . '/dungeons.php';
    require_once __DIR__ . '/missions.php';

    if ($region !== 'jp') // todo, chocobo expeds?
        require_once __DIR__ . '/expeditions.php';