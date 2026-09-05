<statamic:collection:projects
    sort="title:asc"
    as="projects"
>
    <x-section class="bg-dotted">
        <x-grid class="xl:grid-cols-4">
            @foreach ($projects as $project)
                <div class="overflow-hidden rounded-4 bg-white shadow">
                    @if ($project->image)
                        <a
                            href="{{ $project->website }}"
                            target="_blank"
                            rel="noreferrer noopener"
                            class="block pb-1"
                        >
                            <x-img
                                :src="$project->image"
                                width="768"
                                ratio="16:9"
                                :alt="$project->value('title')"
                            />
                        </a>
                    @endif
                    <div class="px-4 py-2">
                        <a
                            href="{{ $project->website }}"
                            target="_blank"
                            rel="noreferrer noopener"
                            class="block hover:text-brand"
                        >
                            <strong>{{ $project->value('title') }}</strong>
                        </a>
                        <p class="text-sm text-snow-20">{{ $project->value('description') }}</p>
                    </div>
                </div>
            @endforeach
        </x-grid>
    </x-section>
</statamic:collection:projects>
