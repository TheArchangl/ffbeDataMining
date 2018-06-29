<?php
    /**
     * User: aEnigma
     * Date: 09.02.2017
     * Time: 12:00
     */

    require_once dirname(__DIR__) . "/bootstrap.php";

    // "update" client files
    require_once "client_update.php";

    // read strings
    if ($region == 'gl')
        require_once "generate_strings.php";

    // units and beasts
    require_once "units.php";
    require_once "summons.php";

    // skills
    require_once "skills.php";
    require_once "enhancements.php";

    // items
    require_once "items.php";
    require_once "recipes.php";

    // mogking
    require_once "event.php";

    // world -> missions
    require_once "worlds.php";
    require_once "dungeons.php";
    require_once "missions.php";

    if ($region != 'jp') // todo, chocobo expeds?
        require_once "expeditions.php";
