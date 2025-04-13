<section>
    <h2>사용자 관리</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>이름</th>
                <th>이메일</th>
                <th>역할</th>
                <th>가입일</th>
                <th>작업</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user->id) ?></td>
                    <td><?= htmlspecialchars($user->name) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td><?= htmlspecialchars($user->role) ?></td>
                    <td><?= htmlspecialchars($user->created_at) ?></td>
                    <td>
                        <form action="/admin/users/delete" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->id) ?>">
                            <button type="submit" onclick="return confirm('정말로 삭제하시겠습니까?');">삭제</button>
                        </form>
                    </td>
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
