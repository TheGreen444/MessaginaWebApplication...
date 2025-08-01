<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kurakani – Start Talking</title>
<style>
    * {margin:0;padding:0;box-sizing:border-box;scroll-behavior:smooth;}
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #0d131d;
        color: #fff;
        min-height: 100vh;
        line-height: 1.6;
    }
    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(8px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 40px;
        z-index: 1000;
    }
    header .logo-text {
        font-size: 1.5em;
        font-weight: bold;
        color: #33cc5b;
    }
    header nav a {
        margin-left: 20px;
        text-decoration: none;
        color: #cce6d3;
        font-weight: bold;
        transition: color 0.3s ease;
    }
    header nav a:hover {
        color: #33cc5b;
    }
    section {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 20px 40px;
        text-align: center;
    }
    .logo {
        font-size: 3em;
        font-weight: bold;
        margin-bottom: 20px;
        color: #33cc5b;
    }
    h2 {
        font-size: 2em;
        margin-bottom: 15px;
        color: #cce6d3;
    }
    p {
        max-width: 600px;
        margin-bottom: 30px;
        font-size: 1.1em;
        color: #b9bbbe;
    }
    a.button {
        background: #b8cfbe;
        color: #0f141f;
        padding: 12px 36px;
        font-size: 1.2em;
        font-weight: bold;
        text-decoration: none;
        border-radius: 30px;
        transition: background 0.2s ease, transform 0.2s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    a.button:hover {
        background: #33cc5b;
        transform: scale(1.05);
    }
    form {
        display: flex;
        flex-direction: column;
        max-width: 400px;
        width: 100%;
    }
    form input, form textarea {
        margin-bottom: 15px;
        padding: 10px;
        border: none;
        border-radius: 5px;
        outline: none;
        font-size: 1em;
    }
    form button {
        background: #93ab99;
        border: none;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
        font-weight: bold;
    }
    form button:hover {
        background: #28a745;
    }
    footer {
        text-align: center;
        padding: 20px;
        color: #72767d;
        background: #0d131d;
    }
</style>
</head>
<body>

<header>
    <div class="logo-text">Kurakani</div>
    <nav>
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="/redrr/?login">Login</a>
    </nav>
</header>

<section id="home">
    <div class="logo">Kurakani</div>
    <h2>Talk with your friends, anywhere.</h2>
    <p>Fast, private, and simple — your conversations, your way. Just one click to get started.</p>
    <a href="/redrr/?register" class="button">Start Talking</a>
</section>

<section id="about">
    <h2>About Us</h2>
    <p>Kurakani is built to connect people with seamless, private communication. Our mission is to make conversations as natural and easy as talking in real life, but with the power of technology behind it.</p>
</section>

<section id="contact">
    <h2>Contact Us</h2>
    <p>Have questions, feedback, or ideas? Send us a message — we’d love to hear from you!</p>
    <form>
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>

<footer>
    &copy; <?php echo date('Y'); ?> Kurakani. All rights reserved.
</footer>
</body>
</html>
