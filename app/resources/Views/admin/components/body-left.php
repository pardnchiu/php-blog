<section id="body-left" class="body-left" :data-min="left.is_body_left_min">
    <button class="mobile" @click="body_left_show">
        <i class="fa-solid fa-chevron-left"></i>
        <span>收合列表</span>
    </button>
    <div>
        <p @click="tab_show">
            <i class="fa-solid fa-newspaper"></i>
            <span>文章管理</span>
            <i class="fa-solid fa-chevron-right"></i>
        </p>
        <section data-row="2">
            <a href="/admin" data-selected="<?php echo preg_match('/^(\/admin$|\/admin\/article\/list)/', $_SERVER['REQUEST_URI']) ? '1' : '0'; ?>">
                <i class="fa-solid fa-table-list"></i>
                <span>文章列表</span>
            </a>
            <a href="/admin/article/add" data-selected="<?php echo preg_match('/^\/admin\/article\/add/', $_SERVER['REQUEST_URI']) ? '1' : '0'; ?>">
                <i class="fa-solid fa-pen"></i>
                <span>文章撰寫</span>
            </a>
        </section>
    </div>
    <a href="/admin/folder/image" title="前往圖片庫範例" data-selected="<?php echo preg_match('/^\/admin\/folder\/image/', $_SERVER['REQUEST_URI']) ? '1' : '0'; ?>">
        <i class="fa-solid fa-photo-film"></i>
        <span>圖片管理</span>
    </a>
    <div>
        <p @click="tab_show">
            <i class="fa-solid fa-folder-open"></i>
            <span>廣告管理</span>
            <i class="fa-solid fa-chevron-right"></i>
        </p>
        <section data-row="2">
            <a href="/admin/banner/top" title="前往圖片庫範例" data-selected="<?php echo preg_match('/^\/admin\/banner\/top/', $_SERVER['REQUEST_URI']) ? '1' : '0'; ?>">
                <i class="fa-solid fa-photo-film"></i>
                <span>頂部廣告</span>
            </a>
            <a href="/admin/banner/bottom" title="前往文檔範例" data-selected="<?php echo preg_match('/^\/admin\/banner\/bottom/', $_SERVER['REQUEST_URI']) ? '1' : '0'; ?>">
                <i class="fa-solid fa-file"></i>
                <span>底部廣告</span>
            </a>
        </section>
    </div>
    <div>
        <p @click="tab_show">
            <i class="fa-solid fa-folder-open"></i>
            <span>網站管理</span>
            <i class="fa-solid fa-chevron-right"></i>
        </p>
        <section data-row="2">
            <a href="./image-folder.html" title="前往圖片庫範例" :data-selected="left.is_folder_image">
                <i class="fa-solid fa-photo-film"></i>
                <span>HEAD</span>
            </a>
            <a href="./file-edit.html" title="前往文檔範例" :data-selected="left.is_file_edit">
                <i class="fa-solid fa-file"></i>
                <span>底部廣告</span>
            </a>
        </section>
    </div>
    <div>
        <p @click="tab_show">
            <i class="fa-solid fa-code-branch"></i>
            <span>版本資訊</span>
            <i class="fa-solid fa-chevron-right"></i>
        </p>
        <section data-row="2">
            <a href="" data-selected="">
                <i class="fa-brands fa-readme"></i>
                <span>Readme</span>
            </a>
            <a href="" data-selected="">
                <i class="fa-solid fa-balance-scale"></i>
                <span>License</span>
            </a>
        </section>
    </div>
    <button class="desktop" @click="body_left_type">
        <i class="fa-solid fa-rotate"></i>
        <span>更改模式</span>
    </button>
</section>