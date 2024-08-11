function showCommentInput() {
  const commentArea = document.querySelector('#commentArea');

  commentArea.classList.toggle('hide');
}

function closeModal() {
  const modal = document.querySelector('#modal');
  console.log(modal);
}

function openModal(resp) {
  return resp;
}
