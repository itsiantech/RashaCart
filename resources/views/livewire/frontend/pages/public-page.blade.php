<div class="pg-public">
    @if ($page->feature_image)
        <div class="pg-hero">
            <img src="{{ asset('storage/' . $page->feature_image) }}" alt="{{ $page->title }}">
        </div>
    @endif

    <div class="container py-4 py-lg-5">
        <div class="pg-sheet">
            <h1 class="pg-title">{{ $page->title }}</h1>

            @if ($page->description)
                <div class="pg-prose">{!! $page->description !!}</div>
            @endif

            @foreach ($blocks as $block)
                @php $type = $block['type'] ?? 'text'; @endphp

                @if ($type === 'text')
                    @if (! empty($block['content']))
                        <div class="pg-section pg-prose">{!! $block['content'] !!}</div>
                    @endif
                @elseif ($type === 'image')
                    @if (! empty($block['image']))
                        <figure class="pg-figure">
                            <img src="{{ asset('storage/' . $block['image']) }}" alt="{{ $block['caption'] ?? '' }}" loading="lazy">
                            @if (! empty($block['caption']))
                                <figcaption>{{ $block['caption'] }}</figcaption>
                            @endif
                        </figure>
                    @endif
                @elseif ($type === 'columns')
                    @php
                        $columns = $block['columns'] ?? [];
                        $colClass = count($columns) === 3 ? 'col-md-4' : 'col-md-6';
                    @endphp
                    @if (count($columns))
                        <div class="row g-4 pg-section pg-columns">
                            @foreach ($columns as $column)
                                <div class="{{ $colClass }}">
                                    @if (($column['type'] ?? 'text') === 'image')
                                        @if (! empty($column['image']))
                                            <figure class="pg-figure mb-0">
                                                <img src="{{ asset('storage/' . $column['image']) }}"
                                                    alt="{{ $column['caption'] ?? '' }}" loading="lazy">
                                                @if (! empty($column['caption']))
                                                    <figcaption>{{ $column['caption'] }}</figcaption>
                                                @endif
                                            </figure>
                                        @endif
                                    @elseif (! empty($column['content']))
                                        <div class="pg-prose">{!! $column['content'] !!}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    </div>

    <style>
        .pg-public { background: #fff; }
        .pg-public .pg-hero img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            display: block;
        }
        .pg-public .pg-sheet {
            max-width: 960px;
            margin: 0 auto;
        }
        .pg-public .pg-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 700;
            color: #292929;
            margin-bottom: 1.25rem;
        }
        .pg-public .pg-section { margin-top: 2rem; }
        .pg-public .pg-prose { color: #41464b; font-size: 1rem; line-height: 1.8; }
        .pg-public .pg-prose > :last-child { margin-bottom: 0; }
        .pg-public .pg-prose h1,
        .pg-public .pg-prose h2,
        .pg-public .pg-prose h3,
        .pg-public .pg-prose h4 {
            color: #292929;
            font-weight: 700;
            line-height: 1.3;
            margin: 1.75rem 0 .75rem;
        }
        .pg-public .pg-prose h1 { font-size: 1.9rem; }
        .pg-public .pg-prose h2 { font-size: 1.5rem; }
        .pg-public .pg-prose h3 { font-size: 1.25rem; }
        .pg-public .pg-prose p { margin-bottom: 1rem; }
        .pg-public .pg-prose img {
            max-width: 100%;
            height: auto;
            border-radius: .6rem;
            margin: .5rem 0;
        }
        .pg-public .pg-prose blockquote {
            border-left: 4px solid var(--shop-color-primary, #EB0000);
            background: #fbf7f7;
            margin: 1.5rem 0;
            padding: .9rem 1.25rem;
            border-radius: .35rem;
            color: #555;
        }
        .pg-public .pg-prose ul,
        .pg-public .pg-prose ol { padding-left: 1.4rem; margin-bottom: 1rem; }
        .pg-public .pg-prose li { margin-bottom: .35rem; }
        .pg-public .pg-prose a { color: var(--shop-color-primary, #EB0000); text-decoration: underline; }
        .pg-public .pg-prose iframe,
        .pg-public .pg-prose video {
            max-width: 100%;
            border-radius: .6rem;
        }
        .pg-public .pg-prose .ql-align-center { text-align: center; }
        .pg-public .pg-prose .ql-align-right { text-align: right; }
        .pg-public .pg-prose .ql-align-justify { text-align: justify; }
        .pg-public .pg-figure { margin: 0; }
        .pg-public .pg-figure img {
            width: 100%;
            height: auto;
            border-radius: .75rem;
            display: block;
        }
        .pg-public .pg-figure figcaption {
            margin-top: .6rem;
            font-size: .875rem;
            color: #7a7f87;
            text-align: center;
        }
        .pg-public .pg-columns .pg-prose { font-size: .98rem; }
        @media (max-width: 575.98px) {
            .pg-public .pg-hero img { max-height: 240px; }
            .pg-public .pg-section { margin-top: 1.5rem; }
        }
    </style>
</div>
