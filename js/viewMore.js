
document.addEventListener('DOMContentLoaded', () => {
    const score = document.querySelector('.score');
    const ratings = document.querySelectorAll('.star-icon input');

    ratings.forEach(rating => {
        rating.addEventListener('change', () => {
            const selectedRating = document.querySelector('.star-icon input:checked').value;
            const text = selectedRating == 1 ? 'star' : 'stars';
            score.textContent = `${selectedRating} ${text}.`;
            updateStars(selectedRating);
        });
    });

    const updateStars = (selectedRating) => {
        const labels = document.querySelectorAll('.star-icon label');
        labels.forEach((label, index) => {
            if (index < selectedRating) {
                label.style.color = 'gold';
            } else {
                label.style.color = 'lightgray';
            }
        });
    };
});










        // document.addEventListener("DOMContentLoaded", () => {
        //     var stars = document.querySelectorAll(".star-icon a");
        //     stars.forEach((item, index1) => {
        //         item.addEventListener("click", (event) => {
        //             event.preventDefault();  // Prevent default anchor behavior
        //             stars.forEach((star, index2) => {
        //                 index1 >= index2 ? star.classList.add("active") : star.classList.remove("active");
        //             });
        //         });
        //     });
        // });