<?php



//admin id 
$io=56887431;
//definisco la variabile univoca per il gruppo
$target_group=-1001266054234;
//variabile univoca che identifica il bot 
$bot_id=1937044709;
//Funzioni del bot
function link_mess($nome,$scritto)
{
global $api;


$mark="Markdown";
$args = array(
'chat_id' => $nome,
'text' => $scritto,
'parse_mode' =>$mark
);

$r = new HttpRequest("get", "https://api.telegram.org/$api/sendMessage", $args);

}


function crea_pdf($id)
{
	$file="database/".$id.".txt";
	$da_convertire=file_get_contents($file);
	//sm($id,$da_convertire);
}



if($msg == "/start" )
{
	//if usato per EVITARE che il bot risponda nel gruppo
	$chat_type=$update["message"]["chat"]["type"];
	$type="private";
	if(strcmp($chat_type,$type)==0){
		sm($chatID, "$type Il bot sta venendo sviluppato, nel dubbio chiedi a @linearubuntu");
	}
}

//tastiera normale

if($msg == "/tastiera" )
{
	//if usato per EVITARE che il bot risponda nel gruppo
	$chat_type=$update["message"]["chat"]["type"];
	$type="private";
	if(strcmp($chat_type,$type)==0){
        $menu[] = array("I_miei_dati");
        $menu[] = array("Cancella_I_miei_dati");


        $text = "Tastiera normale.
Nascondi tastiera: /nascondi $type";
    //solo io posso accedere alla tastiera del bot
        sm($chatID, $text, $menu, '', false, false, false);
		
	}
}

if($msg == "/nascondi")
{
	$chat_type=$update["message"]["chat"]["type"];
	$type="private";
	if(strcmp($chat_type,$type)==0){
		$text = "Tastiera Nascosta.";
      	sm($chatID, $text, 'nascondi');
	}
}

//foto

if($msg == "/foto" && $chatID==$io)
{
	si($chatID, "foto.jpg", false, "questa è la didascalia");
}

//crea file

function crea_file($id,$text,$dir)
{
	$myfile = fopen("$dir/$id.txt", "w");
    fwrite($myfile, $text);
    fclose($myfile);
}

function aggiorna_file($id,$text,$dir)
{
	$myfile = fopen("$dir/$id.txt", "a");
	$text=" ".$text;
    fwrite($myfile, $text);
    fclose($myfile);
}


function elabora($id)
{
	//utilizzo la libreria pspell_link per analizzare le parole del testo
	
	//$pspell_link = pspell_new("en");
	//core del bot, praticamente si occupa di analizzare il file database/$id.txt e di contare occorrenze e parole ecc
	//DEBUG sm($id,"in elaborazione");
	$data_ora=date("Y-m-d H:i:s");
	
	//carico nell'array dati tutto il contenuto del file $id.txt
	$dati=file_get_contents("database/$id.txt");
	$dati=explode(" ",$dati);
	
	//queste due variabili indicano il numero di parole correttamente o erratamente elaborate fino ad ora
	$parole_corrette=0;
	$parole_errate=0;
	
	sm($id,"avvio il ciclo di spell_check");
	//elaboro le parole e ne controllo la correttezza grammaticale 
	    /*foreach ($dati as $parola) {
		sm($id,"ciclo parola : $parola 
corrette : $parole_corrette 
errate : $parole_errate");
    
		if (pspell_check($pspell_link, $parola)) 
		{
			$parole_corrette=$parole_corrette+1;
			sm(id,"parola correttamente digitata");
		} 
		else 
		{
			$parole_errate=$parole_errate+1;
			sm($id,"parola NON correttamente digitata");
		}
	}*/
	
	
	//inizio l'analisi delle parole vere e proprie.
	//conteggio totale parole
	$parole_numero = count($dati);
	
	//valutazione ripetizione parole
	$parole_occorrenze = array_count_values($dati);
	$dir="analizzati";
	
	//vado ad ordinare per parole più utilizzate nel file
    arsort($parole_occorrenze);
	
	$fp = fopen("analizzati/$id.txt", 'w');
    fwrite($fp, print_r($parole_occorrenze, TRUE));
    fclose($fp);
	
	$parole_occorrenza_max=file("analizzati/$id.txt");
	//$parole_occorrenze=file_get_contents("analizzati/$id.txt");
	
	$data_inizio=file_get_contents("data_start/$id.txt");
	//valutazione parole uniche
	$parole_uniche=array_unique($dati);
	$parole_uniche=count($parole_uniche);
	
	//stampa dei risultati, da tenere in conto che è ancora parziale
	sm($id,"
Risultati Analisi

Data inizio acquisizione : $data_inizio
Data ultima acquisizione : $data_ora

Parole corrette : $parole_corrette
Parole errate : $parole_errate
Parole totali : $parole_numero
Parole uniche : $parole_uniche
Parola più usata : ".$parole_occorrenza_max[2]."Parola meno usata : ".$parole_occorrenza_max[(count($parole_occorrenza_max)-2)] 
);

	//sm($id,"fine elaborazione");
}

function elimina($id)
{
	//funzione che permette all'utente di cancellare i suoi record dal bot.
	unlink("analizzati/$id.txt");
	unlink("data_start/$id.txt");
	unlink("database/$id.txt");
	unlink("tempo/$id.txt");
	unlink("user_info/$id.txt");
	unlink("user_old_info/$id.txt");
	
	sm($id,"Cancellazione totale effettuata");
}


function controlla_gruppo($id)
{
	
	$target_group=-1001266054234;
 	//se i messaggi 
	if($id==$target_group){
		//debug
		//sm($id,"il codice $isgroup è identico a $target_group, dunque devo aggiornare ");
		sm($io,"0");
		return 0;
	}else
	{
		//tali messaggi sono di debug, non sono neccessari 
		//sm($id,"il codice è diverso, dunque non aggiorno");
		sm($io,"1");
		
		return 1;
	}
	
}
if($update)
{
	//Sezione variabili e estrazione dal json
    $image_id=$update["message"]["photo"][0]["file_id"];
    $us=$update["message"]["from"]["username"];
    $nom=$update["message"]["from"]["first_name"];
    $conom=$update["message"]["from"]["last_name"];
    $id=$update["message"]["from"]["id"];
	$isgroup=$update["message"]["chat"]["id"];
	$groupname=$update["message"]["chat"]["title"];
	$text=$update["message"]["text"];

    $per="$us+$nom+$conom+$id+$groupname+$isgroup";

    //image_id non utilizzato 
    $imcap=$update["message"]["caption"];
   
    $il_mio_archivio="database/$id.txt";
	
	//check per i comandi del bot 
	$check=0;
	
	//funzione necessaria durante lo sviluppo, impedisce agli altri di usare il bot
	if($id<>$io){$id=0;}
	
	//test di invio dei dati dal bot
	if($text=="I_miei_dati"){
		
		//elabora i dati raccolti fino ad oggi, in alpha
		elabora($id);
		
		//variabile di controllo per evitare di registrare il comando nello storico 
		$check=1;
	}
	
	if($text=="Cancella_I_miei_dati")
	{
		elimina($id);
		
		//variabile di controllo per evitare di registrare il comando nello storico 
		$check=1;
	}
	
	//chiamo la funzione controlla_gruppo, che abilità la raccolta dati SOLO se si tratta di messaggi che vengono dal gruppo
	
	$check = controlla_gruppo($isgroup);
	
	//imposto la variabile nella cartella dove salvare i dati 
	$dir="database";
	
	
	//in questo if, controllo se l'utente ha mai avuto accesso al bot, e se i suoi messaggi vengono dal gruppo indicato 
	if(file_exists("database/$id.txt") && $check==0){
		
		//test per vedere se riesco a prendere anche il caption(stringa sotto) le immagini
		if($image_id){
			$text=$imcap;
		}
		aggiorna_file($id,$text,$dir);
	    
		//controlla se devi aggiornare le info utente
		$old_info=file_get_contents("user_info/$id.txt");
		if(strcmp($old_info, $per) !== 0){
			crea_file($id,$per,"user_info");
			aggiorna_file($id,$per,"user_old_info");
		}
		
		//per domani
		/*
		IMPORTANTE 
		pspell NON FUNZIONA SU ALTERVISTA...........
		
		funziona quasi tutto, i dati vengono correttamente loggati, raccolti, e parzialmente elaborati
		a questo punto, manca tutta l'implementazione che vada a "studiare" la quantità di testo raccolto
		
		I test fino ad ora effettuati sono stati fatti per singolo utente, e non ho idea quanto riuscirà a reggere il bot
		quando lo metteremo sul gruppo, questo sempre a causa di altervista.
		
		Sto guardando alcuni algoritmi per il text mining, e spero di trovare qualcosa di "parzialmente" implementabile, anche se sicuramente dovrò cambiare server
		
		Occhio alle funzioni di aggiornamento dei file, e di scrittura, che al solito funzionano quando si e quando no.
		
		TO DO
		
		
		Poiché l'elaborazione è estremamente laboriosa, devo fare in modo da sfruttare i 200 cron mettendone uno 
		ogni giorno, in maniera tale da avere un aggiornamento dei dati una volta ogni 6/12 ore
		
		
		Fornire una interfaccia chiara di consultazione dei propri dati, probabilmente con "singolo" comando 
		dato da gruppo
		
		DID IT!
		Aggiunto check che NON permette di registrare tutti i messaggi all'infuori di quelli mandati nel gruppo
		
		/start e /tastiera NON FUNZIONANO se chiamati da gruppo
		*/
		
		//crea_pdf($id);
	}else{
		if($check==0){
			if($image_id){
				$text=$imcap;
			}
			//salvo la data di creazione e inizio acquisizione dei dati 
			$today = date("Y-m-d H:i:s");     			// 2001-03-10 17:16:18
			
			//crea il primo file di acquisizione
			crea_file($id,$text,$dir);
			
			//salva la data di avvio acquisizione da parte del bot
			crea_file($id,$today,"data_start");
			
			//salva le info dell'utente  
		    crea_file($id,$per,"user_info");
			
			
		}
	}
}