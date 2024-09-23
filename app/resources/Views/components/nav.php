<!-- <section id="admin-nav">
    <section>
        <a href="/admin">前往後台</a>
    </section>
</section> -->
<nav id="nav">
    <section>
        <a href="/">
            <strong>Debian/PHP</strong>
        </a>
        <ul id="body-tab">
            <li class="dom-temp" :for="item in nav_block.item">
                <a :href="item.href">{{ item.name }}</a>
            </li>
            <li>
                <button>
                    <img src="/image/user.jpg" alt="">
                </button>
            </li>
        </ul>
        <section class="button">
            <button>
                <img src="/image/user.jpg" alt="">
            </button>
            <button @click="tab">
                <i class="fa-solid fa-bars"></i>
            </button>
        </section>
    </section>
</nav>