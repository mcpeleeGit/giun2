<?php
if (!$post) {
    echo "<h2>게시물을 찾을 수 없습니다.</h2>";
    exit;
}
?>

<section>
    <h2><?= htmlspecialchars($post->title) ?></h2>
    <p><strong>작성자:</strong> <?= htmlspecialchars($post->author) ?></p>
    <p><strong>작성일:</strong> <?= htmlspecialchars($post->created_at) ?></p>
    <div>
        <?= nl2br(htmlspecialchars($post->content)) ?>
    </div>
</section>
