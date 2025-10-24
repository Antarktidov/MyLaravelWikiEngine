<script>
  import MarkdownIt from 'markdown-it';
  let { wikiName, articleName, userId, userName, userCanDeleteComments } = $props();

  let comments = $state([]);
  let new_comment = $state('');
  let edited_comment = $state('');

  let edited_comment_id = 0;

  const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const md = new MarkdownIt();


  console.log('Пропсы:', wikiName, articleName, userId, userName, userCanDeleteComments);

  async function start_comments(){ 
    try {
      const res = await fetch(`/api/wiki/${wikiName}/article/${articleName}/comments`);
      let tempComments = await res.json();
      comments = tempComments.data;
      console.log('Комменты:', comments);
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
    
    comments.unshift({
      'id': commentId,
      'user_id': userId,
      'user_name': userName,
      'content': md.render(new_comment),
      'created_at': 'только что',
    });
    console.log('Обновлённые комменты: ', comments);
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

  function openCommentEditor(commentId) {
    closeEditedComment(edited_comment_id)
    edited_comment_id = commentId;
    console.log('Edit btn pressed');
    let comment = comments.find(comment => comment.id === commentId);
    console.log('Комент, выбранный для редактирования:', comment);
    comment.is_editor_open = true;
    edited_comment = comment.content;
  }

  async function saveEditedComment(commentId) {
    let toSaveEditedComment = {
      'content': edited_comment,
    }

    try {
      let response = await fetch(`/api/wiki/${wikiName}/article/${articleName}/comments/${commentId}/update`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json;charset=utf-8',
          'X-CSRF-TOKEN': csrf_token,
        },
        body: JSON.stringify(toSaveEditedComment)
      });

      const result = await response.json();
      
      if (response.ok) {
        let comment = comments.find(comment => comment.id === commentId);
        if (comment) {
          comment.content = md.render(edited_comment);
        }
        
        closeEditedComment(edited_comment_id);
        edited_comment_id = 0;
        console.log('Save btn pressed - success');
      } else {
        console.error('Ошибка при сохранении комментария:', result.error);
        alert('Ошибка при сохранении комментария: ' + result.error);
      }
    } catch (error) {
      console.error('Ошибка сети при сохранении комментария:', error);
      alert('Ошибка сети при сохранении комментария');
    }
  }

  function closeEditedComment(commentId) {
    if (commentId === 0) {
      return;
    }

    console.log('Close btn pressed');
    let comment = comments.find(comment => comment.id === commentId);
    comment.is_editor_open = false;
    edited_comment = '';
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
            {#if comment.is_editor_open == undefined || comment.is_editor_open == null || comment.is_editor_open === false}
              {@html comment.content}
            {/if}
          </div>
          {#if comment.is_editor_open == undefined || comment.is_editor_open == null || comment.is_editor_open === false}
          <div class="ms-auto">
            {#if userId !== 0 && comment.user_id !== 0 && +userId === +comment.user_id}
              <span>
                <button onclick={() => openCommentEditor(comment.id)} class="btn btn-primary">Править</button>
              </span>  
            {/if}
            <span>
              {#if userCanDeleteComments}
               <span>
                <button onclick={() => deleteComment(comment.id)} class="btn btn-danger">Удалить</button>
              </span>
              {/if}
            </span>
          </div>
          {:else}
          <div class="d-flex">
            <textarea bind:value={edited_comment} class="form-control" ></textarea>
            <button onclick={() => closeEditedComment(comment.id)} class="btn btn-danger ms-2">Закрыть</button>
            <button onclick={() => saveEditedComment(comment.id)} class="btn btn-primary ms-2">Сохранить</button>
          </div>
          {/if}
        </div>
      {/each}
    </div>
  {:else}
    <p>Пока нет комментариев.</p>
  {/if}
</div>