<header>
    <img :src="user_block.head" alt="">
    <strong class="dom-temp">{{ user_block.name }}</strong>
    <p class="dom-temp">{{ user_block.title }}</p>
    <section>
        <a class="dom-temp" :href="e.href" :for="e in user_block.contact" target="_blank">
            <i :class="e.icon"></i>
        </a>
    </section>
</header>