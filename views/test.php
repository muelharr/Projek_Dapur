<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Grid Layout</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }

        /* INI ADALAH CSS GRID YANG SEHARUSNYA BERFUNGSI */
        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .recipe-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        .recipe-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }
        .recipe-card-content {
            padding: 1rem;
        }
        .recipe-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
        }
        .recipe-card-meta {
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>
<body>

    <h2>Test Halaman Grid</h2>

    <div class="recipe-grid">
        <div class="recipe-card">
            <img src="https://i.imgur.com/gQY68d1.jpeg" alt="Test Image 1" class="recipe-card-img">
            <div class="recipe-card-content">
                <p class="recipe-card-title">Kartu 1</p>
                <p class="recipe-card-meta">Ini harusnya di samping kartu 2.</p>
            </div>
        </div>
        <div class="recipe-card">
            <img src="https://i.imgur.com/gQY68d1.jpeg" alt="Test Image 2" class="recipe-card-img">
            <div class="recipe-card-content">
                <p class="recipe-card-title">Kartu 2</p>
                <p class="recipe-card-meta">Ini harusnya di samping kartu 1.</p>
            </div>
        </div>
        <div class="recipe-card">
            <img src="https://i.imgur.com/gQY68d1.jpeg" alt="Test Image 3" class="recipe-card-img">
            <div class="recipe-card-content">
                <p class="recipe-card-title">Kartu 3</p>
                <p class="recipe-card-meta">Posisi kartu ketiga.</p>
            </div>
        </div>
        <div class="recipe-card">
            <img src="https://i.imgur.com/gQY68d1.jpeg" alt="Test Image 4" class="recipe-card-img">
            <div class="recipe-card-content">
                <p class="recipe-card-title">Kartu 4</p>
                <p class="recipe-card-meta">Posisi kartu keempat.</p>
            </div>
        </div>
    </div>

</body>
</html>