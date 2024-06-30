#!/usr/bin/env ruby

require 'open-uri'
require 'json'

def get_sessions(event_id, cookie)
  page = 1
  headers = { 'Cookie' => cookie }
  sessions = []
  loop do
    url = "https://events.ringcentral.com/organisers/events/#{event_id}/roundtables.json?page=#{page}"
    resp = JSON.parse(URI.open(url, headers).read)
    sessions += resp['roundtables']
    break if sessions.size >= resp['roundtables_total_count']
    page += 1
  end
  sessions.map do |session|
    [session['name'], session['roundtable_url']]
  end.to_h
end

def escape_sql(str)
  # Rather than trusting we've managed to escape everything, reject anything suspicious.
  # Because we are running this interactively, we can then decide whether to allow the character
  # or find out how to escape it.
  allowlist = "a-zA-Z0-9 .-@+()-_\"éöÅ'ï�ëäüø”íÖńłò!âåóð#ŠůáćžúØŁźŚżśèŻÚÍßÉšÁ’ñ—&–…\t"
  if !str.match?(/^[#{allowlist}]*$/)
    raise "Suspicious string #{str}: " + str.scan(/[^#{allowlist}]/).uniq.join('') 
  end
  $stderr.puts "Warning: � found in #{str}" if str.include?('�')
  str.gsub("'", "''").gsub('�', '')
end

if ARGV.size != 3
  puts "Usage: #{$0} program_url event_id cookie"
  exit 1
end

program_url, event_id, cookie = ARGV

program = JSON.parse(URI.open(program_url).read)
sessions = get_sessions(event_id, cookie)

day1 = Date.parse(program.map {|item| item['datetime']}.min)

stages = []
puts "START TRANSACTION;"
program.each do |item|
  # Format as Mon, Tue, Wed etc.
  date = Date.parse(item['datetime'])
  day_idx = ((date - day1) + 1).to_i
  time = DateTime.parse(item['datetime']).to_time.getlocal('+01:00').strftime('%H:%M')
  title = "Day #{day_idx} - #{date.strftime('%a')} #{time} - #{item['title']}"
  if item['links'].has_key?('replay')
    puts "INSERT INTO prog_replay (item_id, title) VALUES ('#{item['id']}', '#{escape_sql(title)}');"
  end
  if item['links'].has_key?('session')
    if sessions.has_key?(item['title'])
      url = sessions[item['title']]
      puts "INSERT INTO prog_sessions (item_id, title, rce_url) VALUES ('#{item['id']}', '#{escape_sql(title)}', '#{url}');"
    else
      puts "INSERT INTO prog_sessions (item_id, title) VALUES ('#{item['id']}', '#{escape_sql(title)}');"
    end
  end
end
puts "ROLLBACK;"