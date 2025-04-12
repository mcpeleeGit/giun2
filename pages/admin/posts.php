<section>
    <h2>게시물 관리</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post->id) ?></td>
                    <td><?= htmlspecialchars($post->title) ?></td>
                    <td><?= htmlspecialchars($post->author) ?></td>
                    <td><?= htmlspecialchars($post->created_at) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<style>
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}
</style> 