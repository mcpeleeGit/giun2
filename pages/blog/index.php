<section>
    <h2>블로그</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>제목</th>
                <th>작성일</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post->id) ?></td>
                    <td><?= htmlspecialchars($post->title) ?></td>
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

tr:hover {
    background-color: #f5f5f5;
    cursor: pointer;
}
</style>

<script>
document.querySelectorAll('tbody tr').forEach(row => {
    row.onclick = function() {
        const postId = this.children[0].textContent;
        window.location.href = `/blog/${postId}`;
    };
});
</script>