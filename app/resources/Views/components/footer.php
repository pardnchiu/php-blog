<footer>
    <section>
        <p>{{ footer_block.copyright }}</p>
        <ul>
            <li :for="item in footer_block.contact">
                <a :href="item.href" target="_blank">
                    <i :class="item.class"></i>
                </a>
            </li>
        </ul>
    </section>
</footer>