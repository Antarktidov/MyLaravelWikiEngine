<script>
  import { onMount } from 'svelte';

  // получаем пропсы
  let { wikiName, articleName } = $props();

  let comments = $state([]);

  console.log('Пропсы:', wikiName, articleName);

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
</script>

<div class="comments">
  <h3>Комментарии</h3>
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