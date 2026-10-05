import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { releaseNotes, APP_VERSION } from '../../data/releaseNotes';
import { ChevronDown, Sparkles, Bug, Wrench, ArrowLeft, Tag } from 'lucide-react';

const ReleaseNotes = () => {
    const navigate = useNavigate();
    const [expandedVersions, setExpandedVersions] = useState<string[]>([releaseNotes[0]?.version || '']);

    const toggleVersion = (version: string) => {
        setExpandedVersions((prev) => (prev.includes(version) ? prev.filter((v) => v !== version) : [...prev, version]));
    };

    const getTypeBadgeColor = (type: string) => {
        switch (type) {
            case 'major':
                return 'bg-primary/20 text-primary';
            case 'minor':
                return 'bg-info/20 text-info';
            case 'patch':
                return 'bg-success/20 text-success';
            default:
                return 'bg-gray-200 text-gray-600';
        }
    };

    const formatDate = (dateStr: string) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    return (
        <div>
            {/* Header */}
            <div className="flex items-center justify-between flex-wrap gap-4 mb-6">
                <div className="flex items-center gap-3">
                    <button
                        type="button"
                        className="btn btn-outline-secondary btn-sm rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                        onClick={() => navigate(-1)}
                    >
                        <ArrowLeft className="w-4 h-4" />
                    </button>
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <Tag className="w-6 h-6 text-primary" />
                            Release Notes
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Riwayat perubahan dan pembaruan aplikasi — versi terkini{' '}
                            <span className="font-semibold text-primary">v{APP_VERSION}</span>
                        </p>
                    </div>
                </div>
            </div>

            {/* Timeline */}
            <div className="relative">
                {/* Timeline line */}
                <div className="absolute left-[19px] top-0 bottom-0 w-[2px] bg-gray-200 dark:bg-gray-700"></div>

                <div className="space-y-6">
                    {releaseNotes.map((release, index) => {
                        const isExpanded = expandedVersions.includes(release.version);
                        const isLatest = index === 0;

                        return (
                            <div key={release.version} className="relative pl-12">
                                {/* Timeline dot */}
                                <div
                                    className={`absolute left-[12px] top-[18px] w-[16px] h-[16px] rounded-full border-[3px] z-10 ${isLatest
                                            ? 'border-primary bg-primary shadow-md shadow-primary/30'
                                            : 'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-800'
                                        }`}
                                ></div>

                                {/* Version card */}
                                <div className="panel p-0 overflow-hidden">
                                    {/* Card Header — clickable */}
                                    <button
                                        type="button"
                                        className="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors text-left"
                                        onClick={() => toggleVersion(release.version)}
                                    >
                                        <div className="flex items-center gap-3 flex-wrap">
                                            <h2 className="text-lg font-bold text-gray-800 dark:text-white">v{release.version}</h2>
                                            {isLatest && (
                                                <span className="px-2 py-0.5 text-xs font-semibold rounded-full bg-primary text-white">
                                                    Latest
                                                </span>
                                            )}
                                            <span className={`px-2 py-0.5 text-xs font-medium rounded-full ${getTypeBadgeColor(release.type)}`}>
                                                {release.type}
                                            </span>
                                            <span className="text-sm text-gray-500 dark:text-gray-400">{formatDate(release.date)}</span>
                                        </div>
                                        <ChevronDown
                                            className={`w-5 h-5 text-gray-400 transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}`}
                                        />
                                    </button>

                                    {/* Card Body — expandable */}
                                    {isExpanded && (
                                        <div className="border-t border-gray-200 dark:border-gray-700 p-4 space-y-5">
                                            {/* Summary */}
                                            {release.summary && (
                                                <p className="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                                                    {release.summary}
                                                </p>
                                            )}

                                            {/* Features */}
                                            {release.features && release.features.length > 0 && (
                                                <div>
                                                    <h3 className="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2 mb-3">
                                                        <Sparkles className="w-4 h-4 text-amber-500" />
                                                        Fitur ({release.features.length})
                                                    </h3>
                                                    <ul className="space-y-1.5">
                                                        {release.features.map((feat, i) => (
                                                            <li
                                                                key={i}
                                                                className="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300"
                                                            >
                                                                <span className="text-primary mt-1 shrink-0">•</span>
                                                                <span>{feat}</span>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            )}

                                            {/* Bug Fixes */}
                                            {release.bugfixes && release.bugfixes.length > 0 && (
                                                <div>
                                                    <h3 className="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2 mb-3">
                                                        <Bug className="w-4 h-4 text-red-500" />
                                                        Bug Fixes ({release.bugfixes.length})
                                                    </h3>
                                                    <ul className="space-y-1.5">
                                                        {release.bugfixes.map((fix, i) => (
                                                            <li
                                                                key={i}
                                                                className="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300"
                                                            >
                                                                <span className="text-red-500 mt-1 shrink-0">•</span>
                                                                <span>{fix}</span>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            )}

                                            {/* Improvements */}
                                            {release.improvements && release.improvements.length > 0 && (
                                                <div>
                                                    <h3 className="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2 mb-3">
                                                        <Wrench className="w-4 h-4 text-blue-500" />
                                                        Peningkatan ({release.improvements.length})
                                                    </h3>
                                                    <ul className="space-y-1.5">
                                                        {release.improvements.map((imp, i) => (
                                                            <li
                                                                key={i}
                                                                className="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300"
                                                            >
                                                                <span className="text-blue-500 mt-1 shrink-0">•</span>
                                                                <span>{imp}</span>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );
};

export default ReleaseNotes;
