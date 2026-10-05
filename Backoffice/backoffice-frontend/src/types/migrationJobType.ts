export type MigrationJobStatus = 'pending' | 'running' | 'success' | 'failed';

export interface MigrationJobListItem {
  id: number;
  scriptName: string;
  targetDate: string;
  updateColumns: string | null;
  status: MigrationJobStatus;
  requestedBy: number | null;
  requestedByName: string | null;
  requestedAt: string;
  startedAt: string | null;
  finishedAt: string | null;
  exitCode: number | null;
  errorMessage: string | null;
  durationSeconds: number | null;
}

export interface MigrationJobDetail extends MigrationJobListItem {
  command: string | null;
  stdoutLog: string | null;
  stderrLog: string | null;
  workerHost: string | null;
  workerPid: number | null;
}

export interface ScriptRunningStatus {
  scriptName: string;
  isRunning: boolean;
  activeJobId: number | null;
  activeJobStatus: MigrationJobStatus | null;
}

export interface CreateMigrationJobPayload {
  scriptName: string;
  targetDate: string; // YYYY-MM-DD
  updateAllColumns: boolean;
}
