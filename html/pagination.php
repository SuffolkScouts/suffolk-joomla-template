<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

function pagination_list_footer($list)
{
    $html  = '<div class="sf-pagination">';
    $html .= $list['pageslinks'];
    $html .= '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="' . $list['limitstart'] . '">';
    $html .= '</div>';

    return $html;
}

function pagination_list_render($list)
{
    $currentPage = 1;
    $range = 1;
    $step  = 5;

    foreach ($list['pages'] as $k => $page) {
        if (!$page['active']) {
            $currentPage = $k;
        }
    }

    if ($currentPage >= $step) {
        $range = $currentPage % $step === 0
            ? ceil($currentPage / $step) + 1
            : ceil($currentPage / $step);
    }

    $html  = '<ul class="sf-pagination__list">';
    $html .= $list['start']['data'];
    $html .= $list['previous']['data'];

    foreach ($list['pages'] as $k => $page) {
        if (in_array($k, range($range * $step - ($step + 1), $range * $step))) {
            if (($k % $step === 0 || $k === $range * $step - ($step + 1)) && $k !== $currentPage && $k !== $range * $step - $step) {
                $page['data'] = preg_replace('#(<a.*?>).*?(</a>)#', '$1...$2', $page['data']);
            }
        }
        $html .= $page['data'];
    }

    $html .= $list['next']['data'];
    $html .= $list['end']['data'];
    $html .= '</ul>';

    return $html;
}

function pagination_item_active(&$item)
{
    $class   = ' class="sf-pagination__item"';
    $display = null;

    if ($item->text === Text::_('JLIB_HTML_START')) {
        $display = '<span class="fa fa-fast-backward" aria-hidden="true"></span>';
    } elseif ($item->text === Text::_('JPREV')) {
        $display = '<span class="fa fa-backward" aria-hidden="true"></span>';
    } elseif ($item->text === Text::_('JNEXT')) {
        $display = '<span class="fa fa-forward" aria-hidden="true"></span>';
    } elseif ($item->text === Text::_('JLIB_HTML_END')) {
        $display = '<span class="fa fa-fast-forward" aria-hidden="true"></span>';
    }

    if ($display === null) {
        $display = $item->text;
        $class   = ' class="sf-pagination__item sf-pagination__item--page"';
    }

    return '<li' . $class . '><a title="' . $item->text . '" href="' . $item->link . '" class="sf-pagination__link">' . $display . '</a></li>';
}

function pagination_item_inactive(&$item)
{
    if ($item->text === Text::_('JLIB_HTML_START')) {
        return '<li class="sf-pagination__item sf-pagination__item--disabled"><span class="sf-pagination__link"><span class="fa fa-fast-backward" aria-hidden="true"></span></span></li>';
    }

    if ($item->text === Text::_('JPREV')) {
        return '<li class="sf-pagination__item sf-pagination__item--disabled"><span class="sf-pagination__link"><span class="fa fa-backward" aria-hidden="true"></span></span></li>';
    }

    if ($item->text === Text::_('JNEXT')) {
        return '<li class="sf-pagination__item sf-pagination__item--disabled"><span class="sf-pagination__link"><span class="fa fa-forward" aria-hidden="true"></span></span></li>';
    }

    if ($item->text === Text::_('JLIB_HTML_END')) {
        return '<li class="sf-pagination__item sf-pagination__item--disabled"><span class="sf-pagination__link"><span class="fa fa-fast-forward" aria-hidden="true"></span></span></li>';
    }

    if (isset($item->active) && $item->active) {
        return '<li class="sf-pagination__item sf-pagination__item--active sf-pagination__item--page"><span class="sf-pagination__link" aria-current="page">' . $item->text . '</span></li>';
    }

    return '<li class="sf-pagination__item sf-pagination__item--disabled sf-pagination__item--page"><span class="sf-pagination__link">' . $item->text . '</span></li>';
}
