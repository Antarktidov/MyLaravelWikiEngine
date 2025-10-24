<script>
  // получаем пропсы
  let { wikiName, articleName, userId, userName } = $props();

  let comments = $state([]);
  let new_comment = $state('');

  const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  console.log('Пропсы:', wikiName, articleName, userId, userName);

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
    console.log(response);
    console.log('comments[comments.length - 1].id + 1', comments[comments.length - 1].id + 2);
    comments.unshift({
      'id': comments[comments.length - 1].id + 1,
      'user_id': userId,
      'user_name': userName,
      'content': new_comment,
      'created_at': 'только что',
    });
    console.log('Обновлённые комменты: ', comments.data);
    new_comment = '';
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
          {comment.content}
          </div>
        </div>
      {/each}
    </div>
  {:else}
    <p>Пока нет комментариев.</p>
  {/if}
</div>