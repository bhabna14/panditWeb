@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\MenuItem;
    /** @var \App\Models\Admin|null $admin */
    $admin = Auth::guard('admin')->user();
    $menuRoots = MenuItem::treeForAdmin($admin);

    $renderMenu = function ($items) use (&$renderMenu) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            if ($item->type === 'category') {
                echo '<li class="side-item side-item-category">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive);
                }
                continue;
            }

            if ($hasChildren) {
                echo '<li class="slide">';
                echo ' <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">';
                echo ' <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/></svg>';
                echo ' <span class="side-menu__label">' . e($item->title) . '</span>';
                echo ' <i class="angle fas fa-chevron-right"></i>';
                echo ' </a>';
                echo ' <ul class="slide-menu">';
                foreach ($item->childrenRecursive as $child) {
                    echo ' <li><a class="sub-side-menu__item" href="' .
                        e($child->href) .
                        '">' .
                        e($child->title) .
                        '</a></li>';
                }
                echo ' </ul>';
                echo '</li>';
            } else {
                echo '<li class="slide">';
                echo ' <a class="side-menu__item" href="' . e($item->href) . '">';
                echo ' <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M3 13h1v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7h1a1 1 0 0 0 .7-1.7l-9-9a1 1 0 0 0-1.4 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4z"/></svg>';
                echo ' <span class="side-menu__label">' . e($item->title) . '</span>';
                echo ' </a>';
                echo '</li>';
            }
        }
    };
@endphp
