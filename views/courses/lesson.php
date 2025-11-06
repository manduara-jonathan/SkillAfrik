<div class="lesson-container">
    <aside class="lesson-sidebar">
        <h3><?php echo htmlspecialchars($lesson['course_title']); ?></h3>
        <nav class="lesson-nav">
            <ul>
                <?php
                $current_module = '';
                foreach ($course_lessons as $nav_lesson) {
                    if ($nav_lesson['module_title'] !== $current_module) {
                        $current_module = $nav_lesson['module_title'];
                        echo '<li class="module-title">' . htmlspecialchars($current_module) . '</li>';
                    }
                    $active_class = ($nav_lesson['lesson_id'] == $lesson_id) ? 'active' : '';
                    echo '<li class="lesson-item ' . $active_class . '">';
                    echo '<a href="lesson.php?id=' . $nav_lesson['lesson_id'] . '">' . htmlspecialchars($nav_lesson['lesson_title']) . '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </nav>
    </aside>

    <div class="lesson-main-content">
        <div class="lesson-header">
            <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
        </div>

        <div class="lesson-content">
            <?php if ($lesson['content_type'] === 'quiz'): ?>
                <form action="submit_quiz.php" method="POST">
                    <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                    <?php csrf_input(); ?>
                    <?php foreach ($quiz_data as $question_id => $question): ?>
                        <div class="quiz-question">
                            <p><strong><?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                            <?php foreach ($question['answers'] as $answer): ?>
                                <div class="quiz-answer">
                                    <input type="radio" name="answers[<?php echo $question_id; ?>]" value="<?php echo $answer['id']; ?>" id="answer-<?php echo $answer['id']; ?>">
                                    <label for="answer-<?php echo $answer['id']; ?>"><?php echo htmlspecialchars($answer['text']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn-submit-quiz">Soumettre le quiz</button>
                </form>
            <?php elseif ($lesson['content_type'] === 'video'): ?>
                <div class="video-container">
                    <iframe src="<?php echo htmlspecialchars($lesson['content']); ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                </div>
            <?php else: ?>
                <div class="text-content">
                    <?php echo nl2br(htmlspecialchars($lesson['content'])); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="lesson-footer-nav">
            <button class="btn-complete <?php if ($is_completed) { echo 'completed'; } ?>" 
                    data-lesson-id="<?php echo $lesson_id; ?>" 
                    <?php if ($is_completed) { echo 'disabled'; } ?>>
                <?php echo $is_completed ? 'Terminé !' : 'Marquer comme terminé'; ?>
            </button>
        </div>
    </div>
</div>
