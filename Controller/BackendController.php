<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Kanban
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Kanban\Controller;

use Modules\Kanban\Models\KanbanBoardMapper;
use Modules\Kanban\Models\KanbanCardMapper;
use Modules\Kanban\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Kanban backend controller class.
 *
 * @package Modules\Kanban
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function setupStyles(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var \phpOMS\Model\Html\Head $head */
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, '/Modules/Kanban/Theme/Backend/css/styles.css');
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKanbanDashboard(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Kanban/Theme/Backend/kanban-dashboard');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1005801001, $request, $response));

        $list = KanbanBoardMapper
            ::with('columns', models: null)
            ::with('language', $response->getLanguage())
            ::getNewest(20, depth: 3);
        $view->setData('boards', $list);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKanbanBoard(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $board     = KanbanBoardMapper::with('language', $response->getLanguage())
            ::get((int) $request->getData('id'), depth: 5);
        $accountId = $request->header->account;

        if ($board->createdBy->getId() !== $accountId
            && !$this->app->accountManager->get($accountId)->hasPermission(
                PermissionType::READ, $this->app->orgId, $this->app->appName, self::MODULE_NAME, PermissionState::BOARD, $board->getId())
        ) {
            $view->setTemplate('/Web/Backend/Error/403_inline');
            $response->header->status = RequestStatusCode::R_403;
            return $view;
        }

        $view->setTemplate('/Modules/Kanban/Theme/Backend/kanban-board');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1005801001, $request, $response));

        $view->setData('board', $board);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKanbanArchive(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Kanban/Theme/Backend/kanban-archive');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1005801001, $request, $response));

        $list = KanbanBoardMapper::with('columns', models: null)
            ::with('language', $response->getLanguage())
            ::getNewest(20, depth: 3);

        $view->setData('boards', $list);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKanbanBoardCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $accountId = $request->header->account;

        if (!$this->app->accountManager->get($accountId)->hasPermission(
                PermissionType::CREATE, $this->app->orgId, $this->app->appName, self::MODULE_NAME, PermissionState::BOARD)
        ) {
            $view->setTemplate('/Web/Backend/Error/403_inline');
            $response->header->status = RequestStatusCode::R_403;
            return $view;
        }

        $view->setTemplate('/Modules/Kanban/Theme/Backend/kanban-board-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1005801001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKanbanCard(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $card      = KanbanCardMapper::get((int) $request->getData('id'));
        $accountId = $request->header->account;

        if ($card->createdBy->getId() !== $accountId
            && !$this->app->accountManager->get($accountId)->hasPermission(
                PermissionType::READ, $this->app->orgId, $this->app->appName, self::MODULE_NAME, PermissionState::CARD, $card->getId())
        ) {
            $view->setTemplate('/Web/Backend/Error/403_inline');
            $response->header->status = RequestStatusCode::R_403;
            return $view;
        }

        $view->setTemplate('/Modules/Kanban/Theme/Backend/kanban-card');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1005801001, $request, $response));
        $view->setData('card', $card);

        return $view;
    }
}
