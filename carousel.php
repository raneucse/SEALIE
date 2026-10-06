<?php require_once __DIR__ . '/portfolio_data.php'; ?>

<style>
/* Semua class memakai prefix "pfc-" supaya tidak bertabrakan dengan CSS/JS lama (.carousel, .card, dll) */
.pfc-section {
	--pfc-dark: #0f0f12;
	--pfc-medium: #1a1a20;
	--pfc-metal: #3a3a44;
	--pfc-purple: #9945ff;
	--pfc-blue: #14a1ff;

	box-sizing: border-box;
	width: 100%;
	min-height: 100vh;
	min-height: 100svh;
	display: flex;
	align-items: center;
	justify-content: center;
	overflow-x: clip; /* potong sisi samping tanpa membuat area scroll baru */
	position: relative;
}

.pfc-container {
	width: 100%;
	max-width: 1600px;
	height: 600px;
	margin: 0 auto;
	position: relative;
	perspective: 1200px;
}

.pfc-stage {
	position: absolute;
	inset: 0;
	transform-style: preserve-3d;
}

/* Titik tengah item lewat margin; transform hanya untuk efek 3D */
.pfc-item {
	position: absolute;
	left: 50%;
	top: 50%;
	width: 400px;
	height: 500px;
	margin: -250px 0 0 -200px;
	transform-style: preserve-3d;
	transform-origin: center center;
	transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1),
	            opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
	cursor: pointer;
}

.pfc-card {
	width: 100%;
	height: 100%;
	box-sizing: border-box;
	padding: 30px;
	position: relative;
	overflow: hidden;
	background: linear-gradient(135deg, var(--pfc-medium), var(--pfc-dark));
	border: 2px solid var(--pfc-metal);
	border-radius: 20px;
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.pfc-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 1px;
	background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
	animation: pfc-scanline 3s linear infinite;
}

@keyframes pfc-scanline {
	0%   { transform: translateY(0); }
	100% { transform: translateY(500px); }
}

.pfc-number {
	position: absolute;
	top: 20px;
	right: 30px;
	font-size: 72px;
	font-weight: 900;
	color: rgba(153, 69, 255, 0.1);
	font-family: 'Orbitron', monospace;
}

.pfc-image {
	width: 100%;
	height: 200px;
	margin-bottom: 20px;
	position: relative;
	overflow: hidden;
	background: var(--pfc-dark);
	border: 1px solid var(--pfc-metal);
	border-radius: 10px;
}

.pfc-image img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	filter: grayscale(50%) contrast(1.2);
	transition: all 0.5s ease;
}

.pfc-item:hover .pfc-image img {
	filter: grayscale(0%) contrast(1.3);
	transform: scale(1.1);
}

.pfc-body { position: relative; z-index: 1; }

.pfc-title {
	margin: 0 0 8px;
	font-family: 'Orbitron', sans-serif;
	font-size: 22px;
	letter-spacing: 2px;
	text-transform: uppercase;
	background: linear-gradient(90deg, #fff, #c9a8ff);
	-webkit-background-clip: text;
	background-clip: text;
	-webkit-text-fill-color: transparent;
}

.pfc-desc {
	margin: 0 0 14px;
	font-size: 14px;
	line-height: 1.5;
	color: #b5b5c0;
}

.pfc-tech {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin-bottom: 16px;
}

.pfc-tech span {
	padding: 6px 12px;
	font-size: 11px;
	letter-spacing: 1px;
	text-transform: uppercase;
	color: #c9a8ff;
	border: 1px solid rgba(153, 69, 255, 0.6);
	border-radius: 999px;
}

.pfc-btn {
	display: inline-block;
	padding: 12px 28px;
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 3px;
	text-transform: uppercase;
	text-decoration: none;
	color: #fff;
	border-radius: 999px;
	background: linear-gradient(90deg, var(--pfc-purple), var(--pfc-blue));
	box-shadow: 0 8px 20px rgba(153, 69, 255, 0.35);
}
</style>

<section class="pfc-section" id="pfcSection">
	<div class="pfc-container" id="pfcContainer">
		<div class="pfc-stage">
			<?php foreach ($portfolioData as $index => $item): ?>
				<div class="pfc-item" data-index="<?= $index ?>">
					<div class="pfc-card">
						<div class="pfc-number"><?= str_pad($item['id'], 2, '0', STR_PAD_LEFT) ?></div>

						<div class="pfc-image">
							<img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
						</div>

						<div class="pfc-body">
							<h3 class="pfc-title"><?= htmlspecialchars($item['title']) ?></h3>
							<p class="pfc-desc"><?= htmlspecialchars($item['description']) ?></p>

							<a href="#" class="pfc-btn">Explore</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<script>
(function () {
	const section = document.getElementById('pfcSection');
	const container = document.getElementById('pfcContainer');
	const items = Array.from(section.querySelectorAll('.pfc-item'));
	const total = items.length;
	let current = 0;

	// Jika header menutupi bagian atas halaman (fixed), beri ruang sebesar bagian yang tertutup saja
	function fitHeader() {
		const header = document.querySelector('header');
		let overlap = 0;
		if (header && ['fixed', 'absolute'].includes(getComputedStyle(header).position)) {
			const sectionTop = section.getBoundingClientRect().top + window.scrollY - parseFloat(section.style.paddingTop || 0);
			overlap = Math.max(0, header.offsetHeight - sectionTop);
		}
		section.style.paddingTop = overlap + 'px';
	}

	function update() {
		const base = Math.min(1, container.clientWidth / 560); // mengecil di layar sempit
		const spacing = 340 * base;

		items.forEach((item, i) => {
			let offset = i - current;
			if (offset > total / 2) offset -= total;
			if (offset < -total / 2) offset += total;

			const abs = Math.abs(offset);
			item.style.transform =
				`translateX(${offset * spacing}px) translateZ(${-abs * 160}px) ` +
				`rotateY(${-offset * 25}deg) scale(${(1 - abs * 0.06) * base})`;
			item.style.opacity = abs > 3 ? 0 : 1 - abs * 0.22;
			item.style.zIndex = 100 - abs;
			item.style.pointerEvents = abs > 3 ? 'none' : 'auto';
		});
	}

	function go(step) {
		current = (current + step + total) % total;
		update();
	}

	items.forEach((item, i) => {
		item.addEventListener('click', (e) => {
			if (e.target.closest('.pfc-btn')) e.preventDefault();
			if (i !== current) {
				e.preventDefault();
				current = i;
				update();
			}
		});
	});

	// Tombol kontrol ada di index.php (setelah include), jadi dipasang lewat event delegation
	document.addEventListener('click', (e) => {
		if (e.target.closest('#prevBtn')) go(-1);
		if (e.target.closest('#nextBtn')) go(1);
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'ArrowRight') go(1);
		if (e.key === 'ArrowLeft') go(-1);
	});

	let startX = null;
	container.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
	container.addEventListener('touchend', (e) => {
		if (startX === null) return;
		const dx = e.changedTouches[0].clientX - startX;
		if (Math.abs(dx) > 50) go(dx < 0 ? 1 : -1);
		startX = null;
	});

	window.addEventListener('resize', () => { fitHeader(); update(); });
	fitHeader();
	update();
})();
</script>
