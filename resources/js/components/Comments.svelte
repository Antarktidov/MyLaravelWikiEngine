<script>
  import { onMount } from 'svelte';

  // получаем пропсы
  let { wikiName, articleName, userId, userName } = $props();

  let comments = $state([]);
  let new_comment = $state('');

  const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  console.log('Пропсы:', wikiName, articleName, userId, userName);

  async function start_comments(){ 
    try {
      const res = await fetch(`/api/wiki/${wikiName}/article/${articleName}/comments`);
      comments = await res.json();
      console.log('Комменты:', comments);
      console.log(comments.data.length);
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
    comments.data.push({
      'user_id': userId,
      'user_name': userName,
      'content': new_comment,
      'created_at:': Date.now(),
    });
  }
</script>

<div class="comments">
  <h2>Комментарии</h2>
  <h3>Новый комментарий</h3>
  <div class="d-flex">
    <textarea bind:value={new_comment} class="form-control" ></textarea>
    <button onclick={() => postComment()} class="btn btn-primary ms-4">Отправить</button>
  </div>
  {#if comments.data}
    <ul>
      {#each comments.data as comment (comment.content)}
        <li>{comment.content}</li>
      {/each}
    </ul>
  {:else}
    <p>Пока нет комментариев.</p>
  {/if}
</div>