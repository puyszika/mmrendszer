<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MatchLobby;
use App\Models\GameServer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ServerController extends Controller
{
    protected array $servers;

    public function __construct()
    {
        $this->servers = config('cs2servers.servers');
    }

    public function index()
    {
        $servers = $this->servers;

        foreach ($servers as &$server) {
            $sshUser = $server['ssh_user'] ?? 'root';
            $ip = $server['ip'] ?? 'server.versuscs.hu';
            $remotePath = config('cs2servers.base_path') ?? '/mnt/cs2ssd/cs2-multiserver';
            $instance = $server['instance'];

            $remoteCommand = "cd $remotePath && MSM_I_KNOW_WHAT_I_AM_DOING_ALLOW_ROOT=1 ./cs2-server @$instance status";
            $command = ['ssh', "$sshUser@$ip", $remoteCommand];

            $process = new Process($command);
            $process->setTimeout(5); // védelem lassulás ellen
            $process->run();

            $output = $process->getOutput();

            if (str_contains($output, 'RUNNING')) {
                $server['status'] = 'running';
            } elseif (str_contains($output, 'STOPPED') || str_contains($output, 'not running')) {
                $server['status'] = 'stopped';
            } else {
                $server['status'] = 'unknown';
            }
        }

        return view('admin.servers.index', [
            'servers' => $servers
        ]);
    }

    public function start($instance)
    {
        return $this->executeCommand($instance, 'start');
    }

    public function stop($instance)
    {
        return $this->executeCommand($instance, 'stop');
    }

    public function restart($instance)
    {
        return $this->executeCommand($instance, 'restart');
    }

    public function status($instance)
    {
        $server = $this->findServer($instance);

        $sshUser = $server['ssh_user'] ?? 'root';
        $ip = $server['ip'] ?? 'server.versuscs.hu';
        $remotePath = '/mnt/cs2ssd/cs2-multiserver';

        $remoteCommand = "cd $remotePath && MSM_I_KNOW_WHAT_I_AM_DOING_ALLOW_ROOT=1 ./cs2-server @$instance status";
        $command = ['ssh', "$sshUser@$ip", $remoteCommand];

        $process = new \Symfony\Component\Process\Process($command);
        $process->run();
        $output = $process->getOutput();

        if (str_contains($output, 'RUNNING')) {
            $status = 'running';
        } elseif (str_contains($output, 'STOPPED') || str_contains($output, 'not running')) {
            $status = 'stopped';
        } else {
            $status = 'unknown';
        }

        return response()->json([
            'status' => $status,
            'raw_output' => $output,
        ]); 
    }

    // nézet megjelenítő
    public function console($instance)
    {
        return view('admin.servers.console', compact('instance'));
    }

    // parancs végrehajtó
    public function runConsole($instance)
    {
        return $this->executeCommand($instance, 'console');
    }

    public function editConfig($instance)
    {
        $server = $this->findServer($instance);
        $content = $this->sshReadFile($server['ssh_user'], $server['ip'], $server['config_path']);
        /*dd($server['config_path'], $content); // ideiglenesen */
        return view('admin.servers.config', [
            'instance' => $instance,
            'content' => $content,
            'title' => 'Config szerkesztés'
        ]);
    }

    public function saveConfig(Request $request, $instance)
    {
        $server = $this->findServer($instance);
        $this->sshWriteFile($server['ssh_user'], $server['ip'], $server['config_path'], $request->input('content'));
        return redirect()->route('admin.servers.config', $instance);
    }

    public function editWhitelist($instance)
    {
        $server = $this->findServer($instance);
        $content = $this->sshReadFile($server['ssh_user'], $server['ip'], $server['whitelist_path']);
        return view('admin.servers.whitelist', compact('instance', 'content'));
    }

    public function saveWhitelist(Request $request, $instance)
    {
        $server = $this->findServer($instance);
        $this->sshWriteFile($server['ssh_user'], $server['ip'], $server['whitelist_path'], $request->input('content'));
        return redirect()->route('admin.servers.whitelist', $instance);
    }

    public function consoleLog($instance)
    {
        $server = $this->findServer($instance);
        $output = $this->sshReadFile($server['ssh_user'], $server['ip'], $server['log_path']);
        return response($output)->header('Content-Type', 'text/plain');
    }

    protected function executeCommand(string $instance, string $action)
    {
        $server = $this->findServer($instance);

        $sshUser = $server['ssh_user'] ?? 'root';
        $ip = $server['ip'] ?? 'server.versuscs.hu';
        $remotePath = '/mnt/cs2ssd/cs2-multiserver';

        // Dinamikus port beállítás indítás előtt
        if ($action === 'start') {
        $confPath = $server['config_path'];

        // Ha még nem létezik a config fájl, akkor generálunk egyet sablon alapján
        $checkCommand = "ssh {$sshUser}@{$ip} \"if [ -f {$confPath} ]; then echo exists; else echo missing; fi\"";
        $result = trim(shell_exec($checkCommand));

        Log::debug('Config fájl ellenőrzése', [
        'command' => $checkCommand,
        'result' => $result,
        ]);

        if ($result === 'missing') {
            $port = $server['port'] ?? 27015;
            $tvPort = $port + 5;

            $template = Storage::disk('local')->get('server_template.conf');
            $content = preg_replace("/\r\n|\r/", "\n", $content);

            Log::debug('Új config generálása', [
            'port' => $port,
            'tv_port' => $tvPort,
            'path' => $confPath,
            'content' => $content,
            ]);

            $this->sshWriteFile($user, $host, $remotePath, $content);
        }
    }

        // Végrehajtás
        $remoteCommand = "cd $remotePath && MSM_I_KNOW_WHAT_I_AM_DOING_ALLOW_ROOT=1 ./cs2-server @$instance $action";
        $command = ['ssh', "$sshUser@$ip", $remoteCommand];

        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(15);
        $process->run();

        $output = $process->getOutput();
        $status = null;

        if ($action === 'status') {
            if (str_contains($output, 'RUNNING')) {
                $status = 'running';
            } elseif (str_contains($output, 'STOPPED') || str_contains($output, 'not running')) {
                $status = 'stopped';
            } else {
                $status = 'unknown';
            }
        }

        return redirect()->route('admin.servers.index')->with('success', "Szerver $action művelet sikeres.");

    }

    protected function sshReadFile($user, $host, $remotePath)
    {
        $command = ['ssh', "{$user}@{$host}", "cat {$remotePath}"];

        \Log::debug('SSH olvasás command (process):', ['command' => implode(' ', $command)]);

        $process = new \Symfony\Component\Process\Process($command);
        $process->run();

        \Log::debug('SSH STDOUT:', ['output' => $process->getOutput()]);
        \Log::debug('SSH STDERR:', ['error' => $process->getErrorOutput()]);

        return $process->getOutput();
    }


    protected function sshWriteFile($user, $host, $remotePath, $content)
    {
        $tempPath = storage_path("app/temp_" . md5($remotePath) . ".txt");
        file_put_contents($tempPath, $content);
        shell_exec("scp {$tempPath} {$user}@{$host}:{$remotePath}");
        unlink($tempPath);
    }

    protected function findServer($instance): array
    {
        foreach ($this->servers as $server) {
            if ($server['instance'] === $instance) {
                return $server;
            }
        }
        abort(404);
    }

    public function startServerFromLobby($code)
    {
        $lobby = MatchLobby::where('code', $code)->firstOrFail();

        if (!$lobby->final_map) {
            return back()->with('error', 'Még nincs kiválasztott pálya.');
        }

        $server = GameServer::where('status', 'available')->inRandomOrder()->first();

        if (!$server) {
            return back()->with('error', 'Nincs elérhető szerver.');
        }

        // SSH parancs összeállítása
        $command = <<<EOT
    ssh root@{$server->ip} 'cd {$server->path} && ./cs2-server restart {$server->name} tournament'
    EOT;

        try {
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(15);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error("Szerver restart hiba: " . $process->getErrorOutput());
                return back()->with('error', 'Nem sikerült újraindítani a szervert.');
            }

            // Állapot és kapcsolat mentése
            $server->update(['status' => 'in_use']);
            $lobby->update(['game_server_id' => $server->id]);

            return back()->with('success', "Szerver indítva: {$server->name}");

        } catch (\Exception $e) {
            Log::error("SSH hiba: " . $e->getMessage());
            return back()->with('error', 'Hiba történt a szerver elindításakor.');
        }

    }

    public function showLobby($code)
    {
        $lobby = MatchLobby::with('gameServer')->where('code', $code)->firstOrFail();
        return view('admin.lobbies.show', compact('lobby'));
    }
}