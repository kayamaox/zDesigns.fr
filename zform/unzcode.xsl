<?xml version="1.0"?>
<!--
 * Copyright (c) 2010 Fabien CALLUAUD  (smurf1.free.fr, tm-ladder.com)
 * Licensed under the MIT license.
 * http://smurf1.free.fr/sdz/licence.txt
 -->
 
 <!-- ### oubli légende sur les tableaux ### -->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
<xsl:output indent="yes" encoding="UTF-8" method="xml" omit-xml-declaration="yes" />

<xsl:variable name="couleurs">||rose||rouge||orange||jaune||vertc||vertf||olive||turquoise||bleugris||bleu||marine||violet||marron||noir||gris||argent||blanc||</xsl:variable>
<xsl:variable name="polices">||arial||times||courrier||impact||geneva||optima||</xsl:variable>
<xsl:variable name="tailles">||ttpetit||tpetit||petit||gros||tgros||ttgros||</xsl:variable>
<xsl:variable name="formes">||gras||italique||souligne||barre||</xsl:variable>

<!-- TITRES -->
<xsl:template match="h3"><titre1><xsl:value-of select="." /></titre1></xsl:template>
<xsl:template match="h4"><titre2><xsl:value-of select="." /></titre2></xsl:template>

<!-- TABLEAUX -->
<xsl:template match="table"><tableau><xsl:apply-templates /></tableau></xsl:template>
<xsl:template match="tr"><ligne><xsl:apply-templates /></ligne></xsl:template>
<xsl:template match="td"><cellule><xsl:apply-templates /></cellule></xsl:template>
<xsl:template match="th"><entete><xsl:apply-templates /></entete></xsl:template>

<!-- ACRONYMES -->
<xsl:template match="acronym">
	<xsl:element name="acronyme">
		<xsl:attribute name="valeur"><xsl:value-of select="@title" /></xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- INCIDES ET EXPOSANT -->
<xsl:template match="sub"><indice><xsl:apply-templates /></indice></xsl:template>
<xsl:template match="sup"><exposant><xsl:apply-templates /></exposant></xsl:template>

<!-- LISTES -->
<xsl:template match="ul">
	<xsl:element name="liste">
		<xsl:if test="@class!='liste_defaut'">
			<xsl:attribute name="type"><xsl:value-of select="substring-after(@class,'_')" /></xsl:attribute>
		</xsl:if>
	<xsl:apply-templates />
	</xsl:element>
</xsl:template>
<xsl:template match="li"><puce><xsl:apply-templates /></puce></xsl:template>

<!-- COULEURS, POLICES, TAILLES ET MISES EN FORME -->
<xsl:template match="span">
	<xsl:if test="contains($couleurs,concat('||', @class, '||'))"> <!-- COULEURS -->
		<xsl:element name="couleur">
			<xsl:attribute name="nom"><xsl:value-of select="@class" /></xsl:attribute>
			<xsl:apply-templates />
		</xsl:element>
	</xsl:if>
	<xsl:if test="contains($polices,concat('||', @class, '||'))"> <!-- POLICES -->
		<xsl:element name="police">
			<xsl:attribute name="nom"><xsl:value-of select="@class" /></xsl:attribute>
			<xsl:apply-templates />
		</xsl:element>
	</xsl:if>
	<xsl:if test="contains($tailles,concat('||', @class, '||'))"> <!-- TAILLES -->
		<xsl:element name="taille">
			<xsl:attribute name="valeur"><xsl:value-of select="@class" /></xsl:attribute>
			<xsl:apply-templates />
		</xsl:element>	
	</xsl:if>
	<xsl:if test="contains($formes,concat('||', @class, '||'))"> <!-- MISES EN FORME -->
		<xsl:element name="{@class}"><xsl:apply-templates /></xsl:element>	
	</xsl:if>
</xsl:template> 

<!-- REMARQUES (info, attention, ...) -->
<xsl:template match="div[substring(@class,1,3)='rmq']">
	<xsl:element name="{substring-after(@class,' ')}">
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- SECRETS -->
<xsl:template match="div[@class='spoiler3_hidden']">
	<secret><xsl:apply-templates /></secret>
</xsl:template>

<!-- CITATIONS -->
<xsl:template match="div[@class='citation2']">
	<xsl:element name="citation">
		<xsl:if test="@auteur!=''">
			<xsl:attribute name="nom">
				<xsl:value-of select="@auteur" />
			</xsl:attribute>
		</xsl:if>
		<xsl:if test="@lien!=''">
			<xsl:attribute name="lien">
				<xsl:value-of select="@lien" />
			</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- CODE ### A AMELIORER MANQUE LES BALISE debut ET SURLIGNE ### -->
<xsl:template match="div[substring(@class,1,5)='code2']">
	<xsl:element name="code">
		<xsl:if test="@type!=''"><xsl:attribute name="type"><xsl:value-of select="@type" /></xsl:attribute></xsl:if>
		<xsl:if test="@titre!=''"><xsl:attribute name="titre"><xsl:value-of select="@titre" /></xsl:attribute></xsl:if>
		<xsl:if test="@lien!=''"><xsl:attribute name="url"><xsl:value-of select="@lien" /></xsl:attribute></xsl:if>
		<!--<xsl:attribute name="debut"><xsl:value-of select="number(table/tbody/tr/td/pre/.)" /></xsl:attribute>-->
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- attention celà n'est adapté qu'à geshi en mode tableau ! -->
<xsl:template match="div[substring(@class,1,5)='code2']//span"><xsl:apply-templates /></xsl:template>
<xsl:template match="div[substring(@class,1,5)='code2']//tr">	<xsl:apply-templates /></xsl:template>
<xsl:template match="div[substring(@class,1,5)='code2']//table"><xsl:apply-templates /></xsl:template>
<xsl:template match="div[substring(@class,1,5)='code2']//td[1]"></xsl:template>
<xsl:template match="div[substring(@class,1,5)='code2']//td[2]"><xsl:value-of select="." /></xsl:template>

<!-- URLs ET LIENS -->
<xsl:template match="a">
	<xsl:choose>
		<xsl:when test="substring(@href,1,6)='mailto'">
			<xsl:element name="email">
				<xsl:choose>
					<xsl:when test="substring(@href,8)!=."><xsl:attribute name="nom"><xsl:value-of select="@href" /></xsl:attribute><xsl:value-of select="." /></xsl:when>
					<xsl:otherwise><xsl:value-of select="." /></xsl:otherwise>
				</xsl:choose>			
			</xsl:element>
		</xsl:when>
		<xsl:otherwise>
			<xsl:element name="lien">
				<xsl:choose>
					<xsl:when test="@href!=."><xsl:attribute name="url"><xsl:value-of select="@href" /></xsl:attribute><xsl:value-of select="." /></xsl:when>
					<xsl:otherwise><xsl:value-of select="." /></xsl:otherwise>
				</xsl:choose>
			</xsl:element>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- IMAGES -->
<xsl:template match="img">
	<xsl:choose>
		<xsl:when test="substring(@src,1,12)='img/smilies/'">
			<xsl:value-of select="@alt" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:element name="image">
				<xsl:choose>
					<xsl:when test="@alt!='Image utilisateur'"><xsl:attribute name="legende"><xsl:value-of select="@alt" /></xsl:attribute><xsl:value-of select="@src" /></xsl:when>
					<xsl:otherwise><xsl:value-of select="@src" /></xsl:otherwise>
				</xsl:choose>
			</xsl:element>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- POSITION -->
<xsl:template match="div[@class='gauche']|div[@class='droite']|div[@class='justifie']|div[@class='centre']">
	<xsl:element name="position">
		<xsl:attribute name="valeur"><xsl:value-of select="@class" /></xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- FLOTTANTS -->
<xsl:template match="div[@class='flot_gauche']|div[@class='flot_droite']|div[@class='clearer']">
	<xsl:element name="flottant">
		<xsl:attribute name="valeur">
			<xsl:choose>
				<xsl:when test="@class='clearer'"><xsl:value-of select="'aucun'" /></xsl:when>
				<xsl:otherwise><xsl:value-of select="substring(@class,6)" /></xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>


</xsl:stylesheet>