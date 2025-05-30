<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @package FusionCMS
 * @author  Jesper Lindström
 * @author  Xavier Geerinck
 * @author  Elliott Robbins
 * @author  Keramat Jokar (Nightprince) <https://github.com/Nightprince>
 * @author  Ehsan Zare (Darksider) <darksider.legend@gmail.com>
 * @link    https://github.com/FusionWowCMS/FusionCMS
 */

class Realm
{
    // Config
    private int $id;
    private string $name;
    private int $playerCap;
    private array $config;

    // Objects
    private $CI;
    private Characters_model $characters;
    private World_model $world;
    private mixed $emulator;

    // Runtime values
    private int $online;
    private array $onlineFaction;
    private mixed $isOnline;

    /**
     * Initialize the realm
     *
     * @param Int $id
     * @param String $name
     * @param Int $playerCap
     * @param Array $config
     * @param $emulator
     */
    public function __construct(int $id, string $name, int $playerCap, array $config, $emulator)
    {
        // Assign the values
        $this->id = $id;
        $this->name = $name;
        $this->playerCap = $playerCap;
        $this->config = $config;
        $this->config['emulator'] = $emulator;
        $this->isOnline = null;
        $this->onlineFaction = array();

        $overrideParts = array(
            'username',
            'password',
            'hostname',
            'port'
        );

        foreach ($overrideParts as $part) {
            $this->config["override_" . $part . "_char"] = $this->config['characters'][$part];
            $this->config["override_" . $part . "_world"] = $this->config['world'][$part];
        }

        $this->config['characters_database'] = $this->config['characters']['database'];
        $this->config['world_database'] = $this->config['world']['database'];

        // Get the Codeigniter instance
        $this->CI = &get_instance();

        // Load the objects
        require_once('application/models/World_model.php');
        require_once('application/models/Characters_model.php');

        // Make sure the emulator is installed
        if (file_exists('application/emulators/' . $emulator . '.php')) {
            require_once('application/emulators/' . $emulator . '.php');
        } else {
            show_error("The entered emulator (" . $emulator . ") doesn't exist in application/emulators/");
        }

        // Pass the realm ID to the emulator layer
        $config['id'] = $id;

        // Initialize the objects
        $this->emulator = new $emulator($config);
        $this->characters = new Characters_model($config);
        $this->world = new World_model($config);
    }

    /**
     * Get the number of online players
     *
     * @param bool|String $faction horde/alliance
     * @return Int
     */
    public function getOnline(bool|string $faction = false): int
    {
        if (!$faction) {
            if (!empty($this->online)) {
                return $this->online;
            } else {
                // Get the online count
                $cache = $this->CI->cache->get("online_" . $this->id);

                // Can we use the cache?
                if ($cache !== false) {
                    $this->online = $cache;
                } else {
                    // Load and save as cache
                    $this->online = $this->characters->getOnlineCount();

                    // Cache it for 5 minutes
                    $this->CI->cache->save("online_" . $this->id, $this->online, 60 * 5);
                }

                return $this->online;
            }
        } else {
            if (!empty($this->onlineFaction[$faction])) {
                return $this->onlineFaction[$faction];
            } else {
                $cache = $this->CI->cache->get("online_" . $this->id . "_" . $faction);

                // Can we use the cache?
                if ($cache !== false) {
                    $this->onlineFaction[$faction] = $cache;
                } else {
                    // Load and save as cache
                    $this->onlineFaction[$faction] = $this->characters->getOnlineCount($faction);

                    // Cache it for 5 minutes
                    $this->CI->cache->save("online_" . $this->id . "_" . $faction, $this->onlineFaction[$faction], 60 * 5);
                }
            }

            return $this->onlineFaction[$faction];
        }
    }

    /**
     * Get the number of characters that belongs to a certain account
     *
     * @param bool|Int $account
     * @return Int
     */
    public function getCharacterCount(bool|int $account = false): int
    {
        // Default to the current user
        if (!$account) {
            $account = $this->CI->user->getId();
        }

        // Check for cache to use
        $cache = $this->CI->cache->get("total_characters_" . $this->id . "_" . $account);

        // Cache is fresh
        if ($cache !== false) {
            return $cache;
        } else {
            // Refresh cache
            $count = $this->characters->getCharacterCount($account);

            $this->CI->cache->save("total_characters_" . $this->id . "_" . $account, $count, 60 * 60);

            return $count;
        }
    }

    /**
     * Get the percentage of online/cap
     *
     * @param bool|String $faction horde/alliance
     * @return Int
     */
    public function getPercentage(bool|string $faction = false): int
    {
        $online = $faction ? $this->getOnline($faction) : $this->getOnline();
        $cap = $faction ? $this->getOnline() : $this->getCap();

        // Prevent division by zero
        if ($online == 0 || $cap == 0) {
            return 0;
        }

        // Make sure 100 is the max percentage they can get
        if ($online > $cap) {
            return 100;
        }

        // Calculate percentage
        return round(($online / $cap) * 100);
    }

    /**
     * Get the realm name
     *
     * @return String
     */
    public function getName(): string
    {
        return addslashes($this->name);
    }

    /**
     * Get the realm id
     *
     * @return Int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the player cap
     *
     * @return Int
     */
    public function getCap(): int
    {
        return $this->playerCap;
    }

    public function getWorld(): World_model
    {
        return $this->world;
    }

    public function getCharacters(): Characters_model
    {
        return $this->characters;
    }

    public function getEmulator(): emulator
    {
        return $this->emulator;
    }

    public function getExpansionId(): bool|int
    {
        return $this->getConfig('expansion');
    }

    public function getExpansionName(): string
    {
        return $this->getExpansionNameById($this->getExpansionId());
    }

    public function getExpansionSmallName(): string
    {
        $expansions = $this->CI->config->item('expansions_small_name_cn');
        return array_key_exists($this->getExpansionId(), $expansions) ? $expansions[$this->getExpansionId()] : 'default';
    }

    public function getExpansionNameById($id): string
    {
        $expansions = $this->CI->config->item('expansions_name_cn');
        return array_key_exists($id, $expansions) ? $expansions[$id] : 'default';
    }

    public function getExpansionRaces(): array
    {
        $expansion_races = $this->CI->config->item('expansion_races');
        return $expansion_races[$this->getExpansionId()] ?? [];
    }

    public function getExpansionClasses(): array
    {
        $expansion_class = $this->CI->config->item('expansion_class');
        return $expansion_class[$this->getExpansionId()] ?? [];
    }

    /**
     * Check if the realm is up and running
     *
     * @param Boolean $realtime
     * @return bool|null
     */
    public function isOnline(bool $realtime = false): ?bool
    {
        if ($this->isOnline == null) {
            if (!$realtime) {
                $data = $this->CI->cache->get("isOnline_" . $this->getId());

                if ($data !== false) {
                    return $data == "yes";
                }
            }

            if (@fsockopen($this->config['hostname'], $this->config['realm_port'], $errno, $errstr, 1.5)) {
                $this->isOnline = true;
            } else {
                $this->isOnline = false;
            }

            $this->CI->cache->save("isOnline_" . $this->getId(), ($this->isOnline) ? "yes" : "no", 60 * 5);

        }
        return $this->isOnline;
    }

    /**
     * Get config value
     *
     * @param String $key
     * @return false|string|int
     */
    public function getConfig(string $key): mixed
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        } else {
            return false;
        }
    }
}
