import { formatCurrency } from './currency';

interface Section {
  sectionName: string;
  coaCode: string;
  total: number;
  subSection: Section[];
}

export interface FlatReportRow {
  id: string;
  name: string;
  value: string | null;
  level: number;
  type: 'header' | 'item' | 'total';
}

export const flattenReportData = (nodes: Section[]): FlatReportRow[] => {
  const flatList: FlatReportRow[] = [];

  const traverse = (currentNodes: Section[], level: number, parentKey = 'root') => {
    currentNodes.forEach((node, index) => {
      const uniqueKey = `${parentKey}-${index}-${node.coaCode}`;
      const isHeader = node.subSection && node.subSection.length > 0;

      flatList.push({
        id: uniqueKey,
        name: node.sectionName,
        value: null,
        level: level,
        type: isHeader ? 'header' : 'item',
      });

      if (isHeader) {
        traverse(node.subSection, level + 1, uniqueKey);
      }

      flatList.push({
        id: `${uniqueKey}-total`,
        name: `Jumlah ${node.sectionName}`,
        value: formatCurrency(node.total),
        level: level,
        type: 'total',
      });
    });
  };

  traverse(nodes, 0);
  return flatList;
};

export const filterTree = (nodes: Section[], query: string): Section[] => {
  if (!query) {
    return nodes;
  }

  return nodes.reduce((acc, node) => {
    const filteredChildren = filterTree(node.subSection, query);

    const nodeMatches = node.sectionName.toLowerCase().includes(query.toLowerCase());

    if (nodeMatches || filteredChildren.length > 0) {
      acc.push({ ...node, subSection: filteredChildren });
    }

    return acc;
  }, [] as Section[]);
};
