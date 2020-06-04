<?php
    /**
     * User: aEnigma
     * Date: 09.02.2017
     * Time: 12:00
     */

    require_once dirname(__DIR__) . '/bootstrap.php';

    // "update" client files & read strings
    require_once 'client_update.php';

    // mogking
    require_once 'event.php';

    // units and beasts
    require_once 'units.php';
    require_once 'summons.php';

    // skills
    require_once 'skills.php';
    require_once 'enhancements.php';
    require_once 'monster_skills.php'; // monster skills + skillsets
    require_once 'unit_latent_skills.php';

    // items
    require_once 'items.php';
    require_once 'recipes.php';
    if ($region == 'jp')
        require_once 'vision_cards.php';

    // world -> missions
    require_once 'worlds.php';
    require_once 'dungeons.php';
    require_once 'missions.php';

    if ($region != 'jp') // todo, chocobo expeds?
        require_once 'expeditions.php';