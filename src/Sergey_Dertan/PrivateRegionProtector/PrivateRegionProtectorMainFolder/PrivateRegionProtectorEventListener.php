<?php
namespace Sergey_Dertan\PrivateRegionProtector\PrivateRegionProtectorMainFolder;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\WoodenAxe;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as F;


/**
 * Class PrivateRegionProtectorEventListener
 * @package Sergey_Dertan\PrivateRegionProtector\PrivateRegionProtectorMainFolder
 */
class PrivateRegionProtectorEventListener implements Listener
{
    /**
     * @var PrivateRegionProtectorMain
     */
    private $plugin;

    /**
     * @param PrivateRegionProtectorMain $plugin
     */
    function __construct(PrivateRegionProtectorMain $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @return bool
     */
    function CMDUse(PlayerCommandPreprocessEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($this->plugin->checkF($e->getPlayer(), "cmd-use", "deny", false) and !$e->getPlayer()->hasPermission("prp.doall")) {
            $e->setCancelled();
            $e->getPlayer()->sendMessage(F::RED . "[PRP] You can't use commands in this area");
        }
    }

    /**
     * @param PlayerBucketEmptyEvent $e
     * @return bool|void
     */
    function bucketEmpty(PlayerBucketEmptyEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($this->plugin->checkF($e->getPlayer(), "bucket-use", "deny", true) and $e->getPlayer()->hasPermission("prp.doall")) {
            $e->setCancelled();
            $e->getPlayer()->sendMessage(F::RED . "[PRP] You can't use bucket in this area");
            return;
        }
        return;
    }

    /**
     * @param PlayerBucketFillEvent $e
     * @return bool|void
     */
    function bucketFillEvent(PlayerBucketFillEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($this->plugin->checkF($e->getPlayer(), "bucket-use", "deny", true) and $e->getPlayer()->hasPermission("prp.doall")) {
            $e->setCancelled();
            $e->getPlayer()->sendMessage(F::RED . "[PRP] You can't use bucket in this area");
            return;
        }
        return;
    }

    /**
     * @param PlayerDropItemEvent $e
     * @return bool|void
     */
    function ItemDrop(PlayerDropItemEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($this->plugin->checkF($e->getPlayer(), "drop-item", "deny", true) and $e->getPlayer()->hasPermission("prp.doall")) {
            $e->setCancelled();
            $e->getPlayer()->sendMessage(F::RED . "[PRP] You can't drop item in this area");
            return;
        }
        return;
    }

    /**
     * @param PlayerBedEnterEvent $e
     * @return bool
     */
    function noBed(PlayerBedEnterEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($this->plugin->checkF($e->getPlayer(), "sleep", "deny", false) and !$e->getPlayer()->hasPermission("prp.doall")) {
            $e->setCancelled();
            $e->getPlayer()->sendMessage(F::RED . "[PRP] You can't use bed in this area");
        }
    }


    /**
     * @param EntityDamageEvent $e
     * @return bool
     */
    function OffPvP(EntityDamageEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($e instanceof EntityDamageByEntityEvent and $e->getDamager() instanceof Player and $e->getEntity() instanceof Player) {
            if ($this->plugin->checkF($e->getEntity(), "pvp", "deny", false)) {
                $e->setCancelled();
                $e->getDamager()->sendMessage(F::RED . "[PRP] That player in no-pvp area!");
            }
        }
    }

    /**
     * @param EntityDamageEvent $e
     * @return bool
     */
    public function EntityDE(EntityDamageEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($e instanceof EntityDamageEvent) {
            if ($e->getEntity() instanceof Player) {
                if ($this->plugin->checkF($e->getEntity(), "god-mode", "allow", false)) {
                    $e->setCancelled();
                }
            }
        }
    }


    /**
     * @param EntityExplodeEvent $e
     * @return bool
     */
    function EntityExplode(EntityExplodeEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($this->plugin->FEntity($e->getEntity(), "explode", "deny")) {
            $e->setCancelled();
        }
    }

    /**
     * @param EntityCombustEvent $e
     * @return bool
     */
    function EntityCombust(EntityCombustEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        if ($e->getEntity() instanceof Player) {
            if ($this->plugin->checkF($e->getEntity(), "burn", "deny", false)) {
                $e->setCancelled();
            }
        }
    }

    /**
     * @param EntityRegainHealthEvent $e
     * @return bool
     */
    function EntityRegain(EntityRegainHealthEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        $entity = $e->getEntity();
        if ($entity instanceof Player) {
            if ($this->plugin->checkF($e->getEntity(), "regain", "deny", false) and !$entity->hasPermission("prp.doall")) {
                $e->setCancelled();
            }
        }
    }

    /***
     * @param EntityTeleportEvent $e
     * @return bool
     */
    function EntityTeleport(EntityTeleportEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        $entity = $e->getEntity();
        $to = $e->getTo();
        if ($entity instanceof Player) {
            foreach ($this->plugin->areas->getAll() as $name => $area) {
                if ($this->plugin->checkCoordinates($area, $to->getFloorX(), $to->getFloorY(), $to->getFloorZ()) and !(in_array(strtolower($e->getEntity()->getName()), $area["owners"])) and !(in_array(strtolower($e->getEntity()->getName()), $area["members"]))) {
                    $from = $e->getFrom();
                    $e->getEntity()->teleport(new Vector3($from->x, $from->y, $from->z));
                    $e->getEntity()->sendMessage(F::RED . "[PRP] You don`t have permission to teleport here!");
                }
            }
        }
    }


    /***
     * @param PlayerChatEvent $e
     * @return bool
     */
    function PlayerChat(PlayerChatEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        $player = $e->getPlayer();
        if ($this->plugin->checkF($player, "send-chat", "deny", false) and !$player->hasPermission("prp.doall")) {
            if (!$player->hasPermission("prp.doall")) {
                $e->setCancelled();
                $player->sendMessage(F::RED . "[PRP] You can`t use chat in this area!");
            }
        }
    }


    /***
     * @param PlayerQuitEvent $e
     */
    function  PlayerQuit(PlayerQuitEvent $e)
    {
        $playerName = strtolower($e->getPlayer()->getName());
        if (isset($this->plugin->pos1[$playerName])) {
            unset($this->plugin->pos1[$playerName]);
        }
        if (isset($this->plugin->pos2[$playerName])) {
            unset($this->plugin->pos2[$playerName]);
        }
        if (isset($this->plugin->forInfo[$playerName])) {
            unset($this->plugin->forInfo[$playerName]);
        }
        if (isset($this->plugin->forInfoCheckPerm[$playerName])) {
            unset($this->plugin->forInfoCheckPerm[$playerName]);
        }
        if (isset($this->plugin->forCF[$playerName])) {
            unset($this->plugin->forCF[$playerName]);
        }
    }


    /**
     * @param PlayerInteractEvent $e
     * @return bool
     */
    function PlayerInteractEvent(PlayerInteractEvent $e)
    {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        if ($e->isCancelled()) {
            return true;
        }
        if (!in_array($block->getId(), array(Block::CHEST, Block::TRAPDOOR, Block::WOOD_DOOR_BLOCK, Block::DOOR_BLOCK, Block::IRON_DOOR_BLOCK, Block::FURNACE, Block::BURNING_FURNACE))) {
            return true;
        }
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        if (!$e->getItem() instanceof WoodenAxe and $e->getItem()->getID() != 334) {
            if (count($this->plugin->areas->getAll()) != 0) {
                foreach ($this->plugin->areas->getAll() as $area => $info) {
                    if (in_array(strtolower($player->getName()), $info["owners"]) || in_array(strtolower($player->getName()), $info["members"]) || $player->hasPermission("prp.doall")) {
                        continue;
                    } else {
                        if ($this->plugin->checkCoordinates($info, $x, $y, $z)) {
                            $e->setCancelled();
                            $player->sendMessage(F::RED . "[PRP] You don`t have permissions!(USE)");
                        } else {
                            continue;
                        }
                    }

                }
            }
        } elseif ($e->getItem() instanceof WoodenAxe) {
            $e->setCancelled();
            $x1 = $block->getFloorX();
            $y1 = $block->getFloorY();
            $z1 = $block->getFloorZ();
            $this->plugin->pos1[strtolower($player->getName())] = array(0 => $x1, 1 => $y1, 2 => $z1, 'level' => $player->getLevel()->getName());
            $player->sendMessage(F::YELLOW . "[PRP] First position set(" . $x1 . ", " . $y1 . ", " . $z1 . ")");
        } elseif ($e->getItem()->getID() == 334) {
            $e->setCancelled();
            foreach ($this->plugin->areas->getAll() as $name => $info) {
                if ($this->plugin->checkCoordinates($info, $x, $y, $z)) {
                    $this->plugin->forInfo[strtolower($player->getName())] = true;
                    $this->plugin->forInfoCheckPerm[strtolower($player->getName())] = true;
                } else {
                    continue;
                }
            }
            if (isset($this->plugin->forInfo[strtolower($player->getName())]) and $this->plugin->forInfo[strtolower($player->getName())] == true) {
                if (!$e->isCancelled()) {
                    $e->setCancelled();
                }
                $e->setCancelled();
                $x1 = $block->getFloorX();
                $y1 = $block->getFloorY();
                $z1 = $block->getFloorZ();
                foreach ($this->plugin->areas->getAll() as $name => $info) {
                    if ($this->plugin->checkCoordinates($info, $x1, $y1, $z1)) {
                        $player->sendMessage(F::YELLOW . "[PRP] Area: " . $name);
                    } else {
                        continue;
                    }
                }
            } else {
                $player->sendMessage(F::RED . "[PRP] Area not exists");
            }
        }
        return true;
    }

    /***
     * @param BlockPlaceEvent $e
     * @return bool
     */
    function BlockPlaceEvent(BlockPlaceEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        foreach ($this->plugin->areas->getAll() as $name => $info) {
            if (in_array(strtolower($player->getName()), $info["owners"]) || in_array(strtolower($player->getName()), $info["members"]) && $info["flags"]["build"] == "allow" || $player->hasPermission("prp.doall")) {
                continue;
            } else {
                if ($this->plugin->checkCoordinates($info, $x, $y, $z)) {
                    $e->setCancelled();
                    $player->sendMessage(F::RED . "[PRP] You don`t have permissions!(BLOCK_PLACE)");
                } else {
                    continue;
                }
            }
        }
    }

    /***
     * @param BlockBreakEvent $e
     * @return bool
     */
    function BlockBreakEvent(BlockBreakEvent $e)
    {
        if ($e->isCancelled()) {
            return true;
        }
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        if (!$e->getItem() instanceof WoodenAxe) {
            foreach ($this->plugin->areas->getAll() as $name => $info) {
                if (in_array(strtolower($player->getName()), $info["owners"]) || (in_array(strtolower($player->getName()), $info["members"])) || ($info["flags"]["build"] == "allow") || ($player->hasPermission("prp.doall"))) {
                } else {
                    if ($this->plugin->checkCoordinates($info, $x, $y, $z)) {
                        $e->setCancelled();
                        $player->sendMessage(F::RED . "[PRP] You don`t have permissions!(BLOCK_BREAK)");
                    } else {
                        continue;
                    }
                }
            }
        } else {
            $e->setCancelled();
            $pplayer = $e->getPlayer();
            $bblock = $e->getBlock();
            $xx2 = $bblock->getFloorX();
            $yy2 = $bblock->getFloorY();
            $zz2 = $bblock->getFloorZ();
            $this->plugin->pos2[strtolower($pplayer->getName())] = array(0 => $xx2, 1 => $yy2, 2 => $zz2, 'level' => $player->getLevel()->getName());
            $pplayer->sendMessage(F::YELLOW . "[PRP] Second position set(" . $xx2 . ", " . $yy2 . ", " . $zz2 . ")");
        }
    }
}
