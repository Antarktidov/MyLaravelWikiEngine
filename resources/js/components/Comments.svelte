<script>
  import MarkdownIt from 'markdown-it';
  // получаем пропсы
  let { wikiName, articleName, userId, userName, userCanDeleteComments } = $props();

  let comments = $state([]);
  let new_comment = $state('');

  const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const md = new MarkdownIt();


  console.log('Пропсы:', wikiName, articleName, userId, userName, userCanDeleteComments);

  async function start_comments(){ 
    try {
      const res = await fetch(`/api/wiki/${wikiName}/article/${articleName}/comments`);
      let tempComments = await res.json();
      comments = tempComments.data;
      console.log('Комменты:', comments);
      //console.log(comments.data.length);
    } catch (e) {
      console.error('Ошибка загрузки комментариев:', e);
    }
  }
  start_comments();
  async function postComment() {
    let comment = {
      'content': new_comment
    };

    let response = await fetch(`/api/wiki/${wikiName}/article/${articleName}/comments/store`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      'X-CSRF-TOKEN': csrf_token,
    },
      body: JSON.stringify(comment)
    });
    const resJson = await response.json();
    const commentId = resJson.id;
    console.log('comments[comments.length - 1].id + 1', comments[comments.length - 1].id + 2);
    comments.unshift({
      'id': commentId,
      'user_id': userId,
      'user_name': userName,
      'content': md.render(new_comment),
      'created_at': 'только что',
    });
    console.log('Обновлённые комменты: ', comments.data);
    new_comment = '';
  }

  async function deleteComment(commentId) {
    console.log('Delete btn pressed');
    let response = await fetch(`/api/wiki/${wikiName}/article/${articleName}/comments/${commentId}/delete`, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      'X-CSRF-TOKEN': csrf_token,
    },
    });
    comments = comments.filter(comment => comment.id !== commentId);
    console.log(response);
  }

</script>

<div class="comments">
  <h2>Комментарии</h2>
  <h3>Новый комментарий</h3>
  <div class="d-flex">
    <textarea bind:value={new_comment} class="form-control" ></textarea>
    <button onclick={() => postComment()} class="btn btn-primary ms-4">Отправить</button>
  </div>
  {#if comments}
    <div>
      {#each comments as comment, index (comment.id + '-' + index)}
        <div class="card mt-4 p-2">
          <div class="d-flex">
            <span class="fw-bold">{comment.user_name}</span><span class="ms-auto fst-italic text-secondary">{comment.created_at}</span>
          </div>
          <div class="p2 mt-2">
          {@html comment.content}
          </div>
          <div class="ms-auto">
            <span>
              {#if userCanDeleteComments}
               <button onclick={() => deleteComment(comment.id)} class="btn btn-danger">Удалить</button>
              {/if}
            </span>
          </div>
        </div>
      {/each}
    </div>
  {:else}
    <p>Пока нет комментариев.</p>
  {/if}
</div>