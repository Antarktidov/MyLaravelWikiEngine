import './bootstrap';


import Comments from './components/Comments.svelte'

const el = document.getElementById('comments')
if (el) {
    new Comments({
        target: el,
        props: {
            wikiName: el.dataset.wikiName,
            articleName: el.dataset.articleName,
            userId: el.dataset.userId,
            userName: el.dataset.userName,
        }
    })
}