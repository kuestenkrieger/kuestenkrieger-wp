<?php get_header(); ?>

<main class="photo-set grow pt-14 px-6">
    <section class="max-w-250 mx-auto text-center mb-8">
        <?php
        $models = get_post_meta(get_the_ID(), '_portfolio_models', true);
        $link = get_post_meta(get_the_ID(), '_client_link', true);
        $date = get_post_meta(get_the_ID(), '_shooting_date', true);
        ?>
        <?php if ($date) : ?>
            <div class="font-bold text-offwhite-dark"><?php echo date_i18n('m.Y', strtotime($date)); ?></div>
        <?php endif; ?>
        <h1 class="mb-0 leading-[0.8]"><?php the_title(); ?></h1>
    </section>

    <section class="content">
        <?php the_content(); ?>
    </section>

    <section class="bg-neutral-200 p-6 mb-8 text-xs uppercase text-offwhite-dark space-y-2">
        <?php if (!empty($models) && is_array($models)) : ?>
            <div>
                <strong>Models:</strong>
                <?php
                $model_links = [];
                foreach ($models as $model) {
                    if (!empty($model['url'])) {
                        $model_links[] = '<a href="' . esc_url($model['url']) . '" target="_blank" rel="nofollow noopener" class="underline hover:text-black">' . esc_html($model['name']) . '</a>';
                    } else {
                        $model_links[] = esc_html($model['name']);
                    }
                }
                echo implode(', ', $model_links);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($link) : ?>
            <div><strong>Kunde:</strong> <a href="<?php echo esc_url($link); ?>" target="_blank" class="underline hover:text-black">Website besuchen</a></div>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>
