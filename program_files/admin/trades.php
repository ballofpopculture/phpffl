<?php 

$Action=$_REQUEST['Action'];
$ISADMIN=check_is_admin($USERSID, $LEAGUEID);
//To verify this page is included from admin.php
if($Mode=="trades" && $ISADMIN==1)
{

	Switch($Action)
	{
		case "trades_main":
		default:
			display_admin_trades_main();
		break;
		case "cancel_trade":
			$Trades_ID=$_REQUEST['Trades_ID'];
			echo SURE_YOU_WANT_TO_CANCEL."<br><br>";
			echo "<a href='$PHP_SELF?Mode=$Mode&Action=confirm_cancel_trade&Trades_ID=$Trades_ID'>".YES."</a> | <a href='$PHP_SELF?Mode=$Mode&Action='>".NO."</a><br><br>";
			display_trade_detail($Trades_ID);
		break;
		case "confirm_cancel_trade":
			$Trades_ID=$_REQUEST['Trades_ID'];
			$sql="update trades set team2_approval_status='Cancelled' where ID=$Trades_ID;";
			$DB->Execute($sql);
			
			$sql="select team1_ID, team2_ID from trades where ID=$Trades_ID;";
			$rs=$DB->Execute($sql);
			if(!($rs->EOF))
			{
				$Team1_ID=$rs->fields("team1_ID");
				$Team2_ID=$rs->fields("team2_ID");
				$Users_Team=get_users_on_team($Team1_ID);
				$team_approving_name=get_team_name($Team2_ID);
				$Email_subject=TRADE_CANCELLED;
				$Email_message=ADMIN_CANCELLED_TRADE_OFFER;
				foreach($Users_Team as $key => $val)
				{
					$users_email=get_users_email($val);
					if(strlen($users_email)>0)
					{
						send_email($users_email, $Email_subject, $Email_message);
					}			
				}
			}
			echo "<span class='thirteen_title'>".TRADE_CANCELLED."</span><br>";
			
			display_admin_trades_main();
		break;
		case "select_teams_trade":
			display_admin_select_teams_trade();
		break;
		case "propose_trade":
			$Team1_ID=$_REQUEST['Team1_ID'];
			$Team2_ID=$_REQUEST['Team2_ID'];
			$Team1_Players=$_REQUEST['Team1_Players'];
			$Team2_Players=$_REQUEST['Team2_Players'];
			$Team1_Draft=$_REQUEST['Team1_Draft'];
			$Team2_Draft=$_REQUEST['Team2_Draft'];
			$Remove_Player_ID=$_REQUEST['Remove_Player_ID'];
			$Remove_Pick_ID=$_REQUEST['Remove_Pick_ID'];
			$Trade_Type=$_REQUEST['Trade_Type'];

			$errors="";
			if($Team1_ID==$Team2_ID)
			{
				$errors=$errors.TRADE_TEAMS_SELECT_DIFFERENT_TEAMS;
			}
			if(strlen($errors)<1)
			{

				if(strlen($Remove_Player_ID)>0)
				{
					if($Trade_Type=="trade")
					{
						if(!(is_array($Team1_Players)))
						{
							$Team1_Players=explode(":", $Team1_Players);
						}
						foreach($Team1_Players as $key => $val)
						{
							if($val!=$Remove_Player_ID)
								$Team1_Players_array[]=$val;
						}
						$Team1_Players=$Team1_Players_array;
					}
					else
					{
						if(!(is_array($Team2_Players)))
						{
							$Team2_Players=explode(":", $Team2_Players);
						}
						foreach($Team2_Players as $key => $val)
						{
							if($val!=$Remove_Player_ID)
								$Team2_Players_array[]=$val;
						}
						$Team2_Players=$Team2_Players_array;
					
					}
				}
				if(strlen($Remove_Pick_ID)>0)
				{
					
					if($Trade_Type=="trade")
					{
						
						if(!(is_array($Team1_Draft)))
						{
							$Team1_Draft=explode(":", $Team1_Draft);
						}
						foreach($Team1_Draft as $key => $val)
						{
							if($val!=$Remove_Pick_ID)
								$Team1_Draft_array[]=$val;
						}
						$Team1_Draft=$Team1_Draft_array;
					}
					else
					{
						if(!(is_array($Team2_Draft)))
						{
							$Team2_Draft=explode(":", $Team2_Draft);
						}
						foreach($Team2_Draft as $key => $val)
						{
							if($val!=$Remove_Pick_ID)
								$Team2_Draft_array[]=$val;
						}
						$Team2_Draft=$Team2_Draft_array;
					
					}
				}
				display_propose_trade($Team1_ID, $Team2_ID, $Team1_Players, $Team2_Players, $Team1_Draft, $Team2_Draft, 1);
			}
			else
			{
				echo "<span class='errors'>$errors</span>";
				display_admin_select_teams_trade($Team1_ID, $Team2_ID);
			}
		break;
		case "display_add_player_trade":
			$Team1_ID=$_REQUEST['Team1_ID'];
			$Team2_ID=$_REQUEST['Team2_ID'];
			$Team1_Players=$_REQUEST['Team1_Players'];
			$Team2_Players=$_REQUEST['Team2_Players'];
			$Team1_Draft=$_REQUEST['Team1_Draft'];
			$Team2_Draft=$_REQUEST['Team2_Draft'];
			$Trade_Type=$_REQUEST['Trade_Type'];
			display_add_player_trade($Team1_ID, $Team2_ID, $Team1_Players, $Team2_Players, $Team1_Draft, $Team2_Draft, $Trade_Type);
		break;
		case "display_add_pick_trade":
			$Team1_ID=$_REQUEST['Team1_ID'];
			$Team2_ID=$_REQUEST['Team2_ID'];
			$Team1_Players=$_REQUEST['Team1_Players'];
			$Team2_Players=$_REQUEST['Team2_Players'];
			$Team1_Draft=$_REQUEST['Team1_Draft'];
			$Team2_Draft=$_REQUEST['Team2_Draft'];
			$Trade_Type=$_REQUEST['Trade_Type'];
			$Trade_Year=$_REQUEST['Trade_Year'];
			display_add_pick_trade($Team1_ID, $Team2_ID, $Team1_Players, $Team2_Players, $Team1_Draft, $Team2_Draft, $Trade_Type, $Trade_Year);
		break;
		case "propose_trade_confirmation":
			$Team1_ID=$_REQUEST['Team1_ID'];
			$Team2_ID=$_REQUEST['Team2_ID'];
			$Team1_Players=$_REQUEST['Team1_Players'];
			$Team2_Players=$_REQUEST['Team2_Players'];
			$Team1_Draft=$_REQUEST['Team1_Draft'];
			$Team2_Draft=$_REQUEST['Team2_Draft'];
			$Days_Valid=$_REQUEST['Days_Valid'];
			display_confirm_trade_detail($Team1_ID, $Team2_ID, $Team1_Players, $Team2_Players, $Team1_Draft, $Team2_Draft, $Days_Valid);
		break;
		case "confirm_propose_trade":
			$Team1_ID=$_REQUEST['Team1_ID'];
			$Team2_ID=$_REQUEST['Team2_ID'];
			$Team1_Players=$_REQUEST['Team1_Players'];
			$Team2_Players=$_REQUEST['Team2_Players'];
			$Team1_Draft=$_REQUEST['Team1_Draft'];
			$Team2_Draft=$_REQUEST['Team2_Draft'];
			$Days_Valid=$_REQUEST['Days_Valid'];
			$current_gmt_date=gmdate("Y-m-d H:i:s");
			$trade_valid_timestamp=mktime(gmdate("H"),gmdate("i"), gmdate("s"), gmdate("m"), gmdate("d")+$Days_Valid,gmdate("y"));
			$trade_valid_date=date("Y-m-d H:i:s", $trade_valid_timestamp);
			
			if(!(is_array($Team1_Players)))
			{
				$Team1_Players=explode(":", $Team1_Players);
			}
			if(!(is_array($Team2_Players)))
			{
				$Team2_Players=explode(":", $Team2_Players);
			}
			if(!(is_array($Team1_Draft)))
			{
				$Team1_Draft=explode(":", $Team1_Draft);
			}
			if(!(is_array($Team2_Draft)))
			{
				$Team2_Draft=explode(":", $Team2_Draft);
			}
			$errors="";
			//Check to make sure maximum roster requirements not exceeded
			$team1_players_position_totals=get_players_position_totals_team($Team1_ID);
			$team2_players_position_totals=get_players_position_totals_team($Team2_ID);
			$roster_requirements_array=get_roster_requirements_array($LEAGUEID);
			$positions_ID_in_trade_array=array();
			foreach($Team1_Players as $key => $val)
			{
				$players_positions_ID=get_players_positions_ID($val);
				$positions_ID_in_trade_array[$players_positions_ID]=1;
				$team1_players_position_totals[$players_positions_ID]--;
				$team2_players_position_totals[$players_positions_ID]++;
			}
			foreach($Team2_Players as $key => $val)
			{
				$players_positions_ID=get_players_positions_ID($val);
				$positions_ID_in_trade_array[$players_positions_ID]=1;
				$team1_players_position_totals[$players_positions_ID]++;
				$team2_players_position_totals[$players_positions_ID]--;
			}
			foreach($roster_requirements_array as $key => $val)
			{
				if($team1_players_position_totals[$key] > $roster_requirements_array[$key]['max_players'] && $roster_requirements_array[$key]['max_players']>0 && array_key_exists($key, $positions_ID_in_trade_array))
				{
					$team1_name=get_team_name($Team1_ID);
					$max_allowed=$roster_requirements_array[$key]['max_players'];
					$position_name=get_lineup_position_name($key, "long");
					$errors=$errors.TRADE_ROSTER_LIMIT_EXCEEDED_WARNING."<br>";
					$errors=str_replace("ACTUAL_TEAM_NAME_HERE", $team1_name , $errors);
					$errors=$errors."$position_name: $max_allowed<br>";
				}
				if($team2_players_position_totals[$key] > $roster_requirements_array[$key]['max_players'] && $roster_requirements_array[$key]['max_players']>0 && array_key_exists($key, $positions_ID_in_trade_array))
				{
					$team2_name=get_team_name($Team2_ID);
					$max_allowed=$roster_requirements_array[$key]['max_players'];
					$position_name=get_lineup_position_name($key, "long");
					$errors=$errors.TRADE_ROSTER_LIMIT_EXCEEDED_WARNING."<br>";
					$errors=str_replace("ACTUAL_TEAM_NAME_HERE", $team2_name , $errors);
					$errors=$errors."$position_name: $max_allowed<br>";
				}
			}
			
			if(strlen($errors)<1)
			{
				$sql="insert into trades  (team1_date_entered, team1_ID, team2_ID, leagues_ID, trade_expiration_date, enteredby_users_ID) values ('$current_gmt_date', '$Team1_ID', '$Team2_ID', '$LEAGUEID', '$trade_valid_date', '$USERSID');";
				$DB->Execute($sql);
				$trades_ID=$DB->Insert_Id();
				foreach($Team1_Players as $key => $val)
				{
					if(strlen($val)>0)
					{
						$sql="select ID from rosters where current_teams_ID=$Team1_ID and players_ID='$val';";
						$rs=$DB->Execute($sql);
						if(!($rs->EOF))
						{
							
							$sql="insert into trades_teams_players (trades_ID, players_ID, leagues_ID, teams_trading_ID, teams_receiving_ID) values ('$trades_ID', '$val', '$LEAGUEID', '$Team1_ID', '$Team2_ID');";						
							$DB->Execute($sql);
						}
					}
				}
				foreach($Team2_Players as $key => $val)
				{
					if(strlen($val)>0)
					{
						$sql="select ID from rosters where current_teams_ID=$Team2_ID and players_ID='$val';";
						$rs=$DB->Execute($sql);
						if(!($rs->EOF))
						{
							
							$sql="insert into trades_teams_players (trades_ID, players_ID, leagues_ID, teams_trading_ID, teams_receiving_ID) values ('$trades_ID', '$val', '$LEAGUEID', '$Team2_ID', '$Team1_ID');";						
							$DB->Execute($sql);
						}
					}
				}
				foreach($Team1_Draft as $key => $val)
				{
					if(strlen($val)>0)
					{
						$Team1_Draft_data_array=explode("|", $val);
						$Draft_Pick_Team_ID=$Team1_Draft_data_array[0];
						$Draft_Pick_Team_Year=$Team1_Draft_data_array[1];
						$Draft_Pick_Team_Round=$Team1_Draft_data_array[2];
						if(strlen($Draft_Pick_Team_ID)>0 && strlen($Draft_Pick_Team_Year)>0 && strlen($Draft_Pick_Team_Round)>0 )
						{
							$sql="insert into trades_teams_players (trades_ID, draft_pick_teams_ID, draft_pick_year, draft_pick_round, leagues_ID, teams_trading_ID, teams_receiving_ID) values ('$trades_ID', '$Draft_Pick_Team_ID', '$Draft_Pick_Team_Year', '$Draft_Pick_Team_Round', '$LEAGUEID', '$Team1_ID', '$Team2_ID');";						
							$DB->Execute($sql);
						}
					}
				}
				foreach($Team2_Draft as $key => $val)
				{
					if(strlen($val)>0)
					{
						$Team2_Draft_data_array=explode("|", $val);
						$Draft_Pick_Team_ID=$Team2_Draft_data_array[0];
						$Draft_Pick_Team_Year=$Team2_Draft_data_array[1];
						$Draft_Pick_Team_Round=$Team2_Draft_data_array[2];
						if(strlen($Draft_Pick_Team_ID)>0 && strlen($Draft_Pick_Team_Year)>0 && strlen($Draft_Pick_Team_Round)>0 )
						{
							$sql="insert into trades_teams_players (trades_ID, draft_pick_teams_ID, draft_pick_year, draft_pick_round, leagues_ID, teams_trading_ID, teams_receiving_ID) values ('$trades_ID', '$Draft_Pick_Team_ID', '$Draft_Pick_Team_Year', '$Draft_Pick_Team_Round', '$LEAGUEID', '$Team2_ID', '$Team1_ID');";						
							$DB->Execute($sql);
						}
					}
				}
				
				//Now Approve Trade
				
				$Trades_ID=$trades_ID;
				$week_ID=get_current_startinglineup_week();
				$current_week_ID=$week_ID;
				$current_date=gmdate("Y-m-d H:i:s");
				$sql="update trades set team2_approval_status='Approved', team2_approval_date='$current_date', approvedby_users_ID=$USERSID where ID=$Trades_ID;;";
				$DB->Execute($sql);
				$sql="select * from trades_teams_players where trades_ID=$Trades_ID;";
				$trades_rs=$DB->Execute($sql);
				while(!($trades_rs->EOF))
				{
					$players_ID=$trades_rs->fields("players_ID");
					$draft_pick_round=$trades_rs->fields("draft_pick_round");
					$draft_pick_teams_ID=$trades_rs->fields("draft_pick_teams_ID");
					$draft_pick_year=$trades_rs->fields("draft_pick_year");
					$teams_trading_ID=$trades_rs->fields("teams_trading_ID");
					$teams_receiving_ID=$trades_rs->fields("teams_receiving_ID");
					if(strlen($players_ID)>0)
					{
						$sql="update rosters set current_teams_ID=$teams_receiving_ID where players_ID='$players_ID' and current_teams_ID='$teams_trading_ID';";
						$DB->Execute($sql);
						$players_game_date=get_players_game_time($players_ID, $week_ID);
						$current_est_date=get_current_EST_date();
						//Check to see if the game has already started for the current week. 
						if($current_est_date>=$players_game_date && $players_game_date!=-1)
						{
							//do nothing because game has already started
							$current_week_ID=$week_ID+1;
						}
						$sql="delete from starting_lineup_actuals where players_ID='$players_ID' and weeks_ID>=$current_week_ID and games_ID>=$current_week_ID and leagues_ID=$LEAGUEID;";
						$DB->Execute($sql);
						$current_week_ID=$week_ID;
					}
					else
					{
						$sql="delete from teams_traded_picks where traded_pick_teams_ID=$draft_pick_teams_ID and pick_round=$draft_pick_round and pick_year=$draft_pick_year and leagues_ID=$LEAGUEID;";
						$DB->Execute($sql);
						$sql="insert into teams_traded_picks (traded_pick_teams_ID, current_teams_ID, pick_round, pick_year, leagues_ID) values ('$draft_pick_teams_ID', '$teams_receiving_ID', '$draft_pick_round', '$draft_pick_year', '$LEAGUEID');";
						$DB->Execute($sql);
					}
					$trades_rs->MoveNext();
				}
				echo "<span class='thirteen_title'>".TRADE_ACCEPTED."</span><br>";
				display_admin_trades_main();
				//
				
			}
			else
			{
				echo "<span class='errors'>$errors</span>";
				display_propose_trade($Team1_ID, $Team2_ID, $Team1_Players, $Team2_Players, $Team1_Draft, $Team2_Draft, 1);
			}
			
		break;
	}
}



?>