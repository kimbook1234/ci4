<?= $this->include('common/header') ?>
    <div style="max-width:900px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1); position:relative; z-index:1;">
        <!-- 검색창 -->
        <form method="get" action="/board/list">
        <input type="hidden" name="boardmaster" value="<?= $boardmaster ?>">
        <div style="margin-bottom:15px; text-align:right;">
            <div style="position:relative; display:inline-block; width:100%; max-width:250px;">
                <input name="search" type="text" placeholder="검색어 입력" style="width:100%; padding:8px 35px 8px 10px; border:1px solid #ccc; border-radius:20px; font-size:14px; box-sizing:border-box;" value="<?= esc($search) ?>">
                <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                    <input type="image" src="<?= base_url('icon/find.png') ?>" style="width:16px; height:16px; object-fit:cover;">
                </span>
            </div>
        </div>
        </form>
        <div style="text-align:left;height:30px">
            <strong><?= $pager->getCurrentPage()?></strong> / <strong><?= $pager->getPageCount();?></strong> 페이지 
        </div>
        <!-- 게시판 테이블 -->
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <tbody>
            <?php if(!empty($rss) && is_array($rss)):?>
                <?php foreach($rss as $rs): ?>
                <tr>
                    <td style="padding:7px 8px; text-align:left; font-size:16px; font-weight:500" colspan="2"><a href="/board/view/<?= $rs['id'] ?>?page=<?= $pager->getCurrentPage()?>&<?=$url?>"><?= esc($rs['title']) ?></a> 
                    <?php if($rs['cmcnt'] > 0): ?> 
                        &nbsp;<span style="color:red;font-size:13px">(<?= $rs['cmcnt']?>)</span>
                    <?php endif ?>
                </td>
                </tr>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:7px 8px; text-align:left; display:flex; align-items:center; gap:5px;">
                        <!-- <img src="/icon/1234.jpg" alt="프로필" style="width:25px; height:25px; border-radius:50%; object-fit:cover;"> -->
                        <span style="color:#636363;font-weight:300"><?= esc($rs['nickname']) ?> · <?= $rs['inputdate'] ?> · 조회수 <?= $rs['viewcount'] ?></span>
                    </td>
                    <td style="padding:7px 8px; text-align:right; width:300px">추천 <?= $rs['upcnt']?>&nbsp;&nbsp;&nbsp;비추천 <?= $rs['downcnt']?></td>
                </tr>
                <?php endforeach ?>
            <?php endif ?>
            </tbody>
        </table>
        <!-- 페이징 -->
        <div style="text-align:center; margin-top:20px; ">
            <?= $pager->links('default', 'paging') ?>
        </div>
        <!-- 글쓰기 버튼 -->
        <div style="text-align:right;">
            <?php $session = session(); ?>
            <?php if ($session->get('logged')): ?>
                <a href="/board/writeForm"><button class="primaryBtn">작성하기</button></a>
            <?php endif; ?>
        </div>
    </div>

<?= $this->include('common/footer') ?>