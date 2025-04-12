<section>
    <h2>블로그</h2>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <strong><?= htmlspecialchars($post['title']) ?></strong>
                <em><?= $post['created_at'] ?></em>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
