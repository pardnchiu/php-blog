(function () {

    document.addEventListener("DOMContentLoaded", function () {

        const body = new PD({
            id: "body",
            data: {
                nav_block: {
                    title: "個人收費部落格",
                    item: [
                        // {
                        //     href: "/",
                        //     name: "BLOG"
                        // },
                    ],
                },
                user_block: {
                    head: "/image/user.jpg",
                    name: "邱敬幃 Pardn Chiu",
                    title: "帕登數位科技負責人",
                    contact: [
                        {
                            icon: "fa-brands fa-github",
                            href: "https://github.com/pardnchiu"
                        },
                        {
                            icon: "fa-brands fa-linkedin-in",
                            href: "https://linkedin.com/in/pardnchiu"
                        },
                        {
                            icon: "fa-regular fa-envelope",
                            href: "mailto:mail@pardn.ltd"
                        }
                    ]
                },
                footer_block: {
                    copyright: "© 帕登數位科技有限公司 Pardn Ltd",
                    contact: [
                        {
                            href: "https://github.com/pardnchiu",
                            class: "fa-brands fa-github"
                        },
                        {
                            href: "https://linkedin.com/pardnchiu",
                            class: "fa-brands fa-linkedin-in"
                        },
                        {
                            href: "mailto:mail@pardn.ltd",
                            class: "fa-regular fa-envelope"
                        }
                    ]
                }
            },
            event: {
                tab: function () {
                    this.$pre().$$class("show") ? this.$pre().class_("show") : this.$pre()._class("show")
                }
            },
            next: function () {
                const br = "blog".$.$sel("section.br");
                if (br) {
                    const top = vh - br.$h;
                    br._style({
                        top: `${(br.$h + 64 > vh) ? top : 0}px`
                    })
                };
            }
        });

        (function () {
            if ("search-keyword".$ == null) return;
            "search-keyword".$._click(function () {
                location.href = `/blog?q=${this.$pre(0).$child(0).value.replace(/\s/g, "%20")}`;
            });
        }());

        (function () {
            if ("search-keyword-bottom".$ == null) return;
            "search-keyword-bottom".$._click(function () {
                location.href = `/blog?q=${this.$pre(0).$child(0).value.replace(/\s/g, "%20")}`;
            });
        }());

        (function () {
            if ("blog".$ == null) return;
            const br = "blog".$.$sel("section.br");
            if (br) {
                const top = vh - br.$h;
                br._style({ top: `${(br.$h + 64 > vh) ? top : 0}px` })
            };
        }());

        (function () {
            updateShadow();
            document.body.addEventListener("scroll", function () {
                updateShadow();
            });

            function updateShadow() {
                const y = document.body.$y;
                const bottom = document.body.scrollHeight - "footer".$.clientHeight - document.body.clientHeight;

                (function () {
                    if (!"nav".$) return;
                    if (y <= 0) return "nav".$.style_("boxShadow");
                    else if (y >= 32) return "nav".$._style({
                        boxShadow: "0 0.25rem 0.5rem rgba(0, 0, 0, 0.1)"
                    });
                    "nav".$._style({
                        boxShadow: `0 ${0.125 * y / 32}rem ${0.25 * y / 32}rem rgba(0, 0, 0, ${0.1 * y / 32})`
                    });
                }());

                (function () {
                    if (!"section.float-bottom".$) return;
                    const value = bottom - y;
                    if (value <= 0) {
                        return "section.float-bottom".$.style_("boxShadow");
                    }
                    else if (value >= 32) {
                        return "section.float-bottom".$._style({
                            boxShadow: "0 -0.25rem 0.5rem rgba(0, 0, 0, 0.1)"
                        });
                    };
                    "section.float-bottom".$._style({
                        boxShadow: `0 -${0.125 * value / 32}rem ${0.25 * value / 32}rem rgba(0, 0, 0, ${0.1 * value / 32})`
                    });
                }());
            };
        })();

        (function () {
            let resizeTimer, resizeW = document.body.clientWidth;
            window.onresize = function () {
                if (
                    (resizeW > 1024 && document.body.clientWidth > 1024) ||
                    resizeW == document.body.clientWidth
                ) return;
                clearTimeout(resizeTimer);

                resizeTimer = setTimeout(() => {
                    resizeW = document.body.clientWidth;
                    clearTimeout(resizeTimer);

                    updatePortfolio();
                }, 0);
            };
        }());
    });
}());