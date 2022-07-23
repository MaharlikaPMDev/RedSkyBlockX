<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use RedCraftPE\RedSkyBlock\Commands\SBSubCommand;
use RedCraftPE\RedSkyBlock\Island;

use CortexPE\Commando\constraint\InGameRequiredConstraint;

class SetSpawn extends SBSubCommand {

  public function prepare(): void {

    $this->addConstraint(new InGameRequiredConstraint($this));
    $this->setPermission("redskyblock.island");
  }

  public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

    $island = $this->plugin->islandManager->getIslandAtPlayer($sender);
    if (!($island instanceof Island)) {

      if ($this->checkIsland($sender)) {

          $island = $this->plugin->islandManager->getIsland($sender);

      } else {

        $message = $this->getMShop()->construct("NO_ISLAND");
        $sender->sendMessage($message);
        return;
      }
    }

    $members = $island->getMembers();
    if (array_key_exists(strtolower($sender->getName()), $members) || $sender->getName() === $island->getCreator() || $sender->hasPermission("redskyblock.admin")) {

      if (array_key_exists(strtolower($sender->getName()), $members) && !$sender->hasPermission("redskyblock.admin")) {

        $islandPermissions = $island->getPermissions();
        $senderRank = $members[strtolower($sender->getName())];

        if (in_array("island.spawn", $islandPermissions[$senderRank])) {

          if ($this->plugin->islandManager->isOnIsland($sender, $island)) {

            $senderPos = $sender->getPosition();
            $spawnPoint = [round($senderPos->x), round($senderPos->y), round($senderPos->z)];
            $island->setSpawnPoint($spawnPoint);

            $message = $this->getMShop()->construct("SPAWN_CHANGED");
            $message = str_replace("{X}", round($senderPos->x), $message);
            $message = str_replace("{Y}", round($senderPos->y), $message);
            $message = str_replace("{Z}", round($senderPos->z), $message);
            $sender->sendMessage($message);
          } else {

            $message = $this->getMShop()->construct("NOT_ON_ISLAND");
            $sender->sendMessage($message);
          }
        } else {

          $message = $this->getMShop()->construct("RANK_TOO_LOW");
          $message = str_replace("{ISLAND_NAME}", $island->getName(), $message);
          $sender->sendMessage($message);
        }
      } else {

        if ($this->plugin->islandManager->isOnIsland($sender, $island)) {

          $senderPos = $sender->getPosition();
          $spawnPoint = [round($senderPos->x), round($senderPos->y), round($senderPos->z)];
          $island->setSpawnPoint($spawnPoint);

          $message = $this->getMShop()->construct("SPAWN_CHANGED");
          $message = str_replace("{X}", round($senderPos->x), $message);
          $message = str_replace("{Y}", round($senderPos->y), $message);
          $message = str_replace("{Z}", round($senderPos->z), $message);
          $sender->sendMessage($message);
        } else {

          $message = $this->getMShop()->construct("NOT_ON_ISLAND");
          $sender->sendMessage($message);
        }
      }
    } else {

      $message = $this->getMShop()->construct("NOT_A_MEMBER_SELF");
      $message = str_replace("{ISLAND_NAME}", $island->getName(), $message);
      $sender->sendMessage($message);
    }
  }
}
