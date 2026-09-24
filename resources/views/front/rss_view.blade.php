<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<rss version="2.0">
    <channel>
        <title>{{ $siteTitle }}</title>
        <link>{{ $siteUrl }}</link>
        <description>{{ $siteTitle }}</description>
        <language>id-ID</language>
        @foreach ($articles as $a)
            <item>
                <title><![CDATA[{{ $a['article_title'] }}]]></title>
                <link>{{ $siteUrl }}/a/{{ $a['article_uri'] }}</link>
                <guid>{{ $siteUrl }}/a/{{ $a['article_uri'] }}</guid>
                <pubDate>{{ \Illuminate\Support\Carbon::parse($a['article_date'])->toRfc2822String() }}</pubDate>
                <description><![CDATA[{!! strip_tags(substr($a['article_content'] ?? '', 0, 300)) !!}]]></description>
            </item>
        @endforeach
    </channel>
</rss>
