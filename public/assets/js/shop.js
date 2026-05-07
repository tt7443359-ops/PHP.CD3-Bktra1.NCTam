function imageZoom(imgID, resultID, lensID) {
    var img, lens, result, cx, cy;
    img = document.getElementById(imgID);
    result = document.getElementById(resultID);
    lens = document.getElementById(lensID);

    if (!img || !result || !lens) return;

    // Attach events once image is fully loaded otherwise dimensions are wrong
    function initZoom() {
        cx = result.offsetWidth / lens.offsetWidth;
        cy = result.offsetHeight / lens.offsetHeight;

        result.style.backgroundImage = "url('" + img.src + "')";
        result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";
    }

    // Pre-init
    if (img.complete) {
        initZoom();
    } else {
        img.onload = initZoom;
    }

    lens.addEventListener("mousemove", moveLens);
    img.addEventListener("mousemove", moveLens);

    lens.addEventListener("touchmove", moveLens);
    img.addEventListener("touchmove", moveLens);

    img.addEventListener("mouseenter", function() {
        result.style.display = "block";
        lens.style.display = "block";
        cx = result.offsetWidth / lens.offsetWidth;
        cy = result.offsetHeight / lens.offsetHeight;
        result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";
    });

    var container = img.parentElement;
    container.addEventListener("mouseleave", function() {
        result.style.display = "none";
        lens.style.display = "none";
    });

    function moveLens(e) {
        var pos, x, y;
        e.preventDefault();
        pos = getCursorPos(e);

        x = pos.x - (lens.offsetWidth / 2);
        y = pos.y - (lens.offsetHeight / 2);

        if (x > img.width - lens.offsetWidth) {
            x = img.width - lens.offsetWidth;
        }
        if (x < 0) {
            x = 0;
        }
        if (y > img.height - lens.offsetHeight) {
            y = img.height - lens.offsetHeight;
        }
        if (y < 0) {
            y = 0;
        }

        lens.style.left = x + "px";
        lens.style.top = y + "px";
        result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";
    }

    function getCursorPos(e) {
        var a, x = 0,
            y = 0;
        e = e || window.event;
        a = img.getBoundingClientRect();
        var clientX = e.clientX || (e.touches && e.touches[0].clientX);
        var clientY = e.clientY || (e.touches && e.touches[0].clientY);
        x = clientX - a.left;
        y = clientY - a.top;
        return {
            x: x,
            y: y
        };
    }
}

/* ── Hero carousel ───────────────────────────────── */
function initHeroCarousel() {
    const slides = document.querySelectorAll('.gw-hero-slide');
    const dots   = document.querySelectorAll('.gw-dot');
    if (!slides.length) return;
    let cur=0, t;
    function go(n){
        if (slides[cur]) slides[cur].classList.remove('active');
        if(dots[cur]) dots[cur].classList.remove('on');
        cur=(n+slides.length)%slides.length;
        if (slides[cur]) slides[cur].classList.add('active');
        if(dots[cur]) dots[cur].classList.add('on');
    }
    function run(){ t=setInterval(()=>go(cur+1),5000); }
    
    let prevBtn = document.getElementById('gwPrev');
    let nextBtn = document.getElementById('gwNext');
    
    if (prevBtn) {
        prevBtn.addEventListener('click',e=>{e.preventDefault();clearInterval(t);go(cur-1);run();});
    }
    if (nextBtn) {
        nextBtn.addEventListener('click',e=>{e.preventDefault();clearInterval(t);go(cur+1);run();});
    }
    
    dots.forEach(d=>d.addEventListener('click',()=>{clearInterval(t);go(+d.dataset.i);run();}));
    run();
}

/* ── Section carousel arrows ─────────────────────── */
function initSectionCarousels() {
    document.querySelectorAll('.gw-carousel-outer').forEach(outer=>{
        const track = outer.querySelector('.gw-carousel-track');
        outer.querySelector('.gw-arr-l')?.addEventListener('click',()=>track.scrollBy({left:-380,behavior:'smooth'}));
        outer.querySelector('.gw-arr-r')?.addEventListener('click',()=>track.scrollBy({left: 380,behavior:'smooth'}));
    });
}
